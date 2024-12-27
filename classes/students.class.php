<?php
require_once 'database.class.php';

class Students {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Retrieve all students
    public function getAllStudents() {
        $sql = "SELECT * FROM students ORDER BY last_name ASC, first_name ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve a student by ID
    public function getStudentById($id) {
        $sql = "SELECT * FROM students WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Log student actions
    private function logAction($userId, $action, $studentId, $description) {
        try {
            if (!isset($_SESSION['user']['id'])) {
                throw new Exception("User session is not set.");
            }
    
            $sql = "INSERT INTO student_logs (user_id, action, student_id, description) 
                    VALUES (:user_id, :action, :student_id, :description)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':action', $action, PDO::PARAM_STR);
            $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Error logging action: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
    

    // Add a new student
    public function addStudent($data) {
        $sql = "INSERT INTO students 
                (student_number, last_name, first_name, middle_name, course, year_level, section, 
                birthdate, sex, address, wmsu_email, personal_email, status) 
                VALUES 
                (:student_number, :last_name, :first_name, :middle_name, :course, :year_level, :section, 
                :birthdate, :sex, :address, :wmsu_email, :personal_email, :status)";

        $stmt = $this->pdo->prepare($sql);
        $this->bindStudentParams($stmt, $data);
        $stmt->execute();

        $this->logAction($_SESSION['user']['id'], 'add', $this->pdo->lastInsertId(), "Added student: " . $data['first_name'] . ' ' . $data['last_name']);
    }

    // Update an existing student
    public function updateStudent($id, $data) {
        $sql = "UPDATE students SET 
                student_number = :student_number, last_name = :last_name, first_name = :first_name, 
                middle_name = :middle_name, course = :course, year_level = :year_level, section = :section, 
                birthdate = :birthdate, sex = :sex, address = :address, wmsu_email = :wmsu_email, 
                personal_email = :personal_email, status = :status 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $this->bindStudentParams($stmt, $data);
        $stmt->execute();

         $this->logAction($_SESSION['user']['id'], 'update', $id, "Updated student: " . $data['first_name'] . ' ' . $data['last_name']);
    }

    // Delete a student by ID
    public function deleteStudent($id) {
        try {
            // Step 1: Retrieve student details for logging
            $student = $this->getStudentById($id); // Fetch the student before deleting
            if (!$student) {
                throw new Exception("Student not found.");
            }
    
            // Step 2: Nullify logs associated with the student
            $sql = "UPDATE student_logs SET student_id = NULL WHERE student_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
    
            // Step 3: Log the deletion action
            $this->logAction(
                $_SESSION['user']['id'],        // User ID
                'delete',                       // Action
                $id,                            // Student ID
                "Deleted student: " . $student['first_name'] . ' ' . $student['last_name'] // Description
            );
    
            // Step 4: Delete the student
            $sql = "DELETE FROM students WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
    
            return true;
        } catch (PDOException $e) {
            die("Error deleting student: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
    
    // Private function to bind parameters
    private function bindStudentParams($stmt, $data) {
        $stmt->bindParam(':student_number', $data['student_number']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':middle_name', $data['middle_name']);
        $stmt->bindParam(':course', $data['course']);
        $stmt->bindParam(':year_level', $data['year_level']);
        $stmt->bindParam(':section', $data['section']);
        $stmt->bindParam(':birthdate', $data['birthdate']);
        $stmt->bindParam(':sex', $data['sex']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':wmsu_email', $data['wmsu_email']);
        $stmt->bindParam(':personal_email', $data['personal_email']);
        $stmt->bindParam(':status', $data['status']);
    }
}

// Initialize the Students class
global $pdo;
$studentsClass = new Students($pdo);

// Initialize variables
$student_number = $last_name = $first_name = $middle_name = $course = $year_level = $section = $birthdate = $sex = $address = $wmsu_email = $personal_email = $status = "";
$student_number_err = $name_err = $course_err = $year_level_err = $section_err = $birthdate_err = $sex_err = $address_err = $wmsu_email_err = $personal_email_err = $status_err = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty(trim($_POST['student_number']))) {
        $student_number_err = "Please enter the student number.";
    } else {
        $student_number = trim($_POST['student_number']);
    }

    // Validate Last Name
    if (empty(trim($_POST['last_name']))) {
        $last_name_err = "Please enter the last name.";
    } else {
        $last_name = trim($_POST['last_name']);
    }

    // Validate First Name
    if (empty(trim($_POST['first_name']))) {
        $first_name_err = "Please enter the first name.";
    } else {
        $first_name = trim($_POST['first_name']);
    }

    // Validate Middle Name
    if (empty(trim($_POST['middle_name']))) {
        $middle_name_err = "Please enter the middle name.";
    } else {
        $middle_name = trim($_POST['middle_name']);
    }


    if (empty(trim($_POST['course']))) {
        $course_err = "Please enter the course.";
    } else {
        $course = trim($_POST['course']);
    }

    if (empty(trim($_POST['year_level']))) {
        $year_level_err = "Please enter the year level.";
    } else {
        $year_level = trim($_POST['year_level']);
    }

    if (empty(trim($_POST['section'] ?? ''))) {
        $section_err = "Please enter the section.";
    } else {
        $section = trim($_POST['section']);
    }

    if (empty(trim($_POST['birthdate']))) {
        $birthdate_err = "Please enter the birthdate.";
    } else {
        $birthdate = trim($_POST['birthdate']);
    }

    if (empty(trim($_POST['sex'] ?? ''))) {
        $sex_err = "Please enter the sex.";
    } else {
        $sex = trim($_POST['sex']);
    }

    if (empty(trim($_POST['address']))) {
        $address_err = "Please enter the address.";
    } else {
        $address = trim($_POST['address']);
    }

    if (empty(trim($_POST['wmsu_email']))) {
        $wmsu_email_err = "Please enter the WMSU email.";
    } else {
        $wmsu_email = trim($_POST['wmsu_email']);
    }

    if (empty(trim($_POST['personal_email']))) {
        $personal_email_err = "Please enter the personal email.";
    } else {
        $personal_email = trim($_POST['personal_email']);
    }

    if (empty(trim($_POST['status'] ?? ''))) {
        $status_err = "Please enter the status.";
    } else {
        $status = trim($_POST['status']);
    }

    if (empty($student_number_err) && empty($name_err) && empty($course_err) && empty($year_level_err) &&
        empty($section_err) && empty($birthdate_err) && empty($sex_err) && empty($address_err) &&
        empty($wmsu_email_err) && empty($personal_email_err) && empty($status_err)) {
        
        $data = [
            'student_number' => $student_number,
            'last_name' => $last_name,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'course' => $course,
            'year_level' => $year_level,
            'section' => $section,
            'birthdate' => $birthdate,
            'sex' => $sex,
            'address' => $address,
            'wmsu_email' => $wmsu_email,
            'personal_email' => $personal_email,
            'status' => $status
        ];

        if (!empty($_POST['update_id'])) {
            $studentsClass->updateStudent($_POST['update_id'], $data);
        } else {
            $studentsClass->addStudent($data);
        }

        header("Location: students.php");
        exit();
    }
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    $studentsClass->deleteStudent($_GET['delete_id']);
    header("Location: students.php");
    exit();
}

// Fetch all students
$students = $studentsClass->getAllStudents();
?>
