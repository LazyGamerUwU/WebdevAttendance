<?php
global $pdo;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables
$event_id = $student_number = $student_name = $time_in = $time_out = $course = "";
$event_id_err = $student_number_err = $time_in_err = $time_out_err = "";

// Fetch all events for the dropdown - modify to match your database structure
$events = $pdo->query("
    SELECT id, title, event_date 
    FROM events 
    ORDER BY event_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Function to log attendance actions
function logAttendanceAction($pdo, $userId, $action, $attendanceId, $description) {
    try {
        // Get additional details for logging
        $stmt = $pdo->prepare("
            SELECT 
                a.student_number,
                a.time_in,
                a.time_out,
                e.title as event_title,
                CONCAT(s.last_name, ', ', s.first_name, ' ', s.middle_name) as student_name
            FROM attendance a
            JOIN events e ON a.event_id = e.id
            JOIN students s ON a.student_number = s.student_number
            WHERE a.id = :attendance_id
        ");
        $stmt->execute(['attendance_id' => $attendanceId]);
        $details = $stmt->fetch(PDO::FETCH_ASSOC);

        // Create detailed description
        $detailedDescription = sprintf(
            "%s - Event: %s, Student: %s (%s), Time In: %s, Time Out: %s",
            $description,
            $details['event_title'],
            $details['student_name'],
            $details['student_number'],
            date('h:i A', strtotime($details['time_in'])),
            $details['time_out'] ? date('h:i A', strtotime($details['time_out'])) : 'Not set'
        );

        // Insert log entry
        $sql = "INSERT INTO attendance_logs (user_id, action, attendance_id, description) 
                VALUES (:user_id, :action, :attendance_id, :description)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'attendance_id' => $attendanceId,
            'description' => $detailedDescription
        ]);
    } catch (PDOException $e) {
        error_log("Error logging attendance action: " . $e->getMessage());
        // Continue execution even if logging fails
    }
}

// Determine redirection path based on user role
function getRedirectPath() {
    if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
        return 'attendance.php';
    } else {
        return 'attendance_officer.php';
    }
}

// Update the event_id fetch near the top
if (isset($_GET['event_id'])) {
    $stmt = $pdo->prepare("SELECT id FROM events WHERE title = :title");
    $stmt->execute(['title' => $_GET['event_id']]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    $event_id = $event['id'];
}

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    if (empty($_POST['event_id'])) {
        $event_id_err = "Please select an event.";
    } else {
        // Get the actual event ID from the title
        $stmt = $pdo->prepare("SELECT id FROM events WHERE title = :title");
        $stmt->execute(['title' => $_POST['event_id']]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($event) {
            $event_id = $event['id'];
        } else {
            $event_id_err = "Invalid event selected.";
        }
    }

    if (empty(trim($_POST['student_number']))) {
        $student_number_err = "Please enter the student number.";
    } else {
        $student_number = trim($_POST['student_number']);
        // Fetch student name and course
        $stmt = $pdo->prepare("
            SELECT 
                CONCAT(last_name, ', ', first_name, ' ', middle_name) AS name, 
                course 
            FROM students 
            WHERE student_number = :student_number
        ");
        $stmt->bindParam(':student_number', $student_number);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $student_name = $student['name'];
            $course = $student['course'];
        } else {
            $student_number_err = "Student not found.";
        }
    }

    if (empty(trim($_POST['time_in']))) {
        $time_in_err = "Please enter the time in.";
    } else {
        $time_in = trim($_POST['time_in']);
    }

    if (empty(trim($_POST['time_out']))) {
        $time_out_err = "Please enter the time out.";
    } elseif ($time_in >= trim($_POST['time_out'])) {
        $time_out_err = "Time out cannot be earlier than or equal to the time in.";
    } else {
        $time_out = trim($_POST['time_out']);
    }

    // Convert time format from 12-hour to 24-hour for database storage
    if (!empty($_POST['time_in'])) {
        $time_in = date('H:i', strtotime($_POST['time_in']));
    }

    if (!empty($_POST['time_out'])) {
        $time_out = date('H:i', strtotime($_POST['time_out']));
        
        // Compare with current time
        $current_time = date('H:i');
        if ($time_out < $current_time) {
            $time_out_err = "Time out cannot be earlier than the current time.";
        }
    }

    // Modified time validation
    if (empty($time_in)) {
        $time_in_err = "Time in is required.";
    }

    if (!empty($time_out)) {
        if ($time_out < $time_in) {
            $time_out_err = "Time out cannot be earlier than time in.";
        }
    }

    // Check input errors before inserting or updating in the database
    if (empty($event_id_err) && empty($student_number_err) && empty($time_in_err) && empty($time_out_err)) {
        try {
            if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
                // Update attendance
                $sql = "UPDATE attendance 
                        SET event_id = :event_id, student_number = :student_number, 
                            time_in = :time_in, time_out = :time_out 
                        WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":event_id", $event_id);
                $stmt->bindParam(":student_number", $student_number);
                $stmt->bindParam(":time_in", $time_in);
                $stmt->bindParam(":time_out", $time_out);
                $stmt->bindParam(":id", $_POST['update_id']);
                $stmt->execute();

                // Log the update action
                logAttendanceAction($pdo, $_SESSION['user']['id'], 'update', $_POST['update_id'], "Updated attendance for student: $student_number");
            } else {
                // Insert new attendance
                $sql = "INSERT INTO attendance (event_id, student_number, time_in, time_out) 
                        VALUES (:event_id, :student_number, :time_in, :time_out)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":event_id", $event_id);
                $stmt->bindParam(":student_number", $student_number);
                $stmt->bindParam(":time_in", $time_in);
                $stmt->bindParam(":time_out", $time_out);
                $stmt->execute();

                // Log the add action
                $attendanceId = $pdo->lastInsertId();
                logAttendanceAction($pdo, $_SESSION['user']['id'], 'add', $attendanceId, "Added attendance for student: $student_number");
            }

            // Redirect with the event title
            header("Location: " . getRedirectPath() . "?event_id=" . urlencode($_POST['event_id']));
            exit();
        } catch (PDOException $e) {
            // Log the error and show a user-friendly message
            error_log("Database Error: " . $e->getMessage());
            echo "An error occurred while saving the attendance. Please try again.";
        }
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    // Fetch attendance details for logging
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE id = :id");
    $stmt->bindParam(":id", $_GET['delete_id']);
    $stmt->execute();
    $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($attendance) {
        // Log the delete action
        logAttendanceAction($pdo, $_SESSION['user']['id'], 'delete', $_GET['delete_id'], "Deleted attendance for student: " . $attendance['student_number']);

        // Delete the attendance
        $sql = "DELETE FROM attendance WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $_GET['delete_id']);
        $stmt->execute();
    }

    // Redirect based on user role
    header("Location: " . getRedirectPath() . "?event_id=" . htmlspecialchars($_GET['event_id']));
    exit();
}

// Add this function to get event details
function getEventDetails($pdo, $event_title) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE title = :title LIMIT 1");
    $stmt->execute(['title' => $event_title]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
