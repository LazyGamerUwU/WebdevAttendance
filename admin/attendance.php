<?php
require '../classes/account.class.php';
require '../classes/database.class.php';
require '../classes/attendance.class.php';
require '../tools/functions.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Redirect if the user is not an admin
Account::redirect_if_not_logged_in('admin');
// Fetch attendance data for the selected event
$attendance_data = [];
$selected_event = null;
if (isset($_GET['event_id'])) {
    $stmt = $pdo->prepare("SELECT id FROM events WHERE title = :title");
    $stmt->execute(['title' => $_GET['event_id']]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($event) {
        $event_id = $event['id'];
        $stmt = $pdo->prepare("
            SELECT 
                a.id, a.student_number, a.time_in, a.time_out, 
                CONCAT(s.last_name, ', ', s.first_name, ' ', s.middle_name) AS student_name, 
                s.course 
            FROM attendance a 
            INNER JOIN students s ON a.student_number = s.student_number 
            WHERE a.event_id = :event_id 
            ORDER BY a.time_in ASC
        ");
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        $attendance_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    
    $sql = "SELECT 
                student_number,
                CONCAT(last_name, ', ', first_name, ' ', COALESCE(middle_name, '')) as name,
                course
            FROM students 
            WHERE student_number LIKE :search 
               OR CONCAT(last_name, ', ', first_name, ' ', COALESCE(middle_name, '')) LIKE :search
               OR course LIKE :search
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $searchTerm = "%{$search}%";
    $stmt->execute(['search' => $searchTerm]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($results);
    exit(); // Important to prevent rest of the page from loading
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../includes/_head.php'; ?>
<link href="../css/dashboard.css" rel="stylesheet">
<link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<body>
<?php include '../includes/_topnav.php'; ?>
<?php include 'modals_admin/edit_attendance_modal.php'; ?>

<div class="container mt-4">
    <h1>Manage Attendance</h1>

    <!-- Event Selection -->
    <form method="GET" class="mb-4">
        <div class="form-group">
            <label for="event_id">Select or Search Event</label>
            <input class="form-control" list="eventsList" id="event_id" name="event_id" 
                   onchange="this.form.submit()" 
                   value="<?= isset($_GET['event_id']) ? htmlspecialchars($_GET['event_id']) : '' ?>">
            <datalist id="eventsList">
                <?php foreach ($events as $event): ?>
                    <option value="<?= htmlspecialchars($event['title']) ?>">
                <?php endforeach; ?>
            </datalist>
        </div>
    </form>

    <?php if (!empty($event_id)): ?>
        <!-- Form for Create and Update -->
        <form method="POST" class="mb-4">
            <input type="hidden" id="update_id" name="update_id">
            <input type="hidden" name="event_id" value="<?= isset($_GET['event_id']) ? htmlspecialchars($_GET['event_id']) : '' ?>">

            <!-- Student Search -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="student_search">Student</label>
                        <input type="text" class="form-control" id="student_search" 
                               placeholder="Search by Student Number, Name, or Course" 
                               autocomplete="off">
                        <div id="searchResults" class="list-group position-absolute w-100" style="z-index: 1000; display: none;">
                            <!-- Search results will be populated here -->
                        </div>
                        <input type="hidden" id="selected_student_number" name="student_number">
                    </div>
                </div>
            </div>

            <!-- Time In and Time Out in One Row -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="time_in">Time In</label>
                        <input type="text" class="form-control" id="time_in" name="time_in" readonly 
                               value="<?= date('h:i A', time()) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="time_out">Time Out</label>
                        <input type="time" class="form-control" id="time_out_raw" 
                               onchange="updateTimeOutDisplay(this.value)">
                        <input type="hidden" id="time_out" name="time_out">
                        <button type="button" class="btn btn-secondary mt-2" id="timeOutBtn">Set Current Time</button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="form-submit-btn">Add Attendance</button>
        </form>


        <!-- Attendance List -->
        <h2>Attendance List</h2>
		<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Student Number</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_data as $entry): ?>
                    <tr>
                        <td><?= htmlspecialchars($entry['student_number']) ?></td>
                        <td><?= htmlspecialchars($entry['student_name']) ?></td>
                        <td><?= htmlspecialchars($entry['course']) ?></td>
                        <td><?= htmlspecialchars($entry['time_in']) ?></td>
                        <td><?= htmlspecialchars($entry['time_out']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-btn" data-id="<?= $entry['id'] ?>" data-student_number="<?= $entry['student_number'] ?>" data-time_in="<?= $entry['time_in'] ?>" data-time_out="<?= $entry['time_out'] ?>">
                                Edit
                            </button>
                            <a href="attendance.php?delete_id=<?= $entry['id'] ?>&event_id=<?= $event_id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<footer class="text-center py-3 mt-5">
    <p class="mb-0">&copy; 2024 Event Management System. All Rights Reserved.</p>
</footer>
<script src="../vendor/jquery/jquery.min.js"></script>
<script>
    document.getElementById('student_number').addEventListener('input', function () {
        const studentNumber = this.value;
        if (studentNumber.length > 0) {
            fetch(`get_student_name.php?student_number=${studentNumber}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('student_name').value = data.name;
                        document.getElementById('student_course').value = data.course;
                    } else {
                        document.getElementById('student_name').value = '';
                        document.getElementById('student_course').value = '';
                    }
                });
        } else {
            document.getElementById('student_name').value = '';
            document.getElementById('student_course').value = '';
        }
    });
</script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
	
<script>
// Single DataTables initialization with time formatting
$(document).ready(function() {
    if (!$.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable({
            "drawCallback": function() {
                formatTimeDisplay();
            }
        });
    }
});

// Keep the time formatting function
function formatTimeDisplay() {
    const rows = document.querySelectorAll('#dataTable tbody tr');
    rows.forEach(row => {
        const timeInCell = row.cells[3];
        const timeOutCell = row.cells[4];
        
        if (timeInCell.textContent) {
            const timeIn = new Date(`2000-01-01 ${timeInCell.textContent}`);
            timeInCell.textContent = timeIn.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true 
            });
        }
        
        if (timeOutCell.textContent) {
            const timeOut = new Date(`2000-01-01 ${timeOutCell.textContent}`);
            timeOutCell.textContent = timeOut.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true 
            });
        }
    });
}
</script>

<script>
$(document).ready(function() {
    // Handle Edit Button Click
    $('.edit-btn').on('click', function() {
        // Get data attributes from the clicked button
        const id = $(this).data('id');
        const studentNumber = $(this).data('student_number');
        const timeIn = $(this).data('time_in');
        const timeOut = $(this).data('time_out');

        // Populate the modal fields with the existing data
        $('#edit_update_id').val(id);
        $('#edit_student_number').val(studentNumber);
        $('#edit_time_in').val(timeIn);
        $('#edit_time_out').val(timeOut);

        // Fetch the student name and course dynamically
        fetch(`get_student_name.php?student_number=${studentNumber}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#edit_student_name').val(data.name);
                    $('#edit_student_course').val(data.course);
                } else {
                    $('#edit_student_name').val('');
                    $('#edit_student_course').val('');
                }
            });

        // Show the modal
        $('#editAttendanceModal').modal('show');
    });
});
</script>

<script>
// Handle event selection and hidden input
document.getElementById('event_id').addEventListener('input', function(e) {
    const datalist = document.getElementById('eventsList');
    const options = datalist.getElementsByTagName('option');
    for (let option of options) {
        if (option.value === this.value) {
            document.getElementById('actual_event_id').value = option.dataset.id;
            break;
        }
    }
});

// Handle Time Out button
document.getElementById('timeOutBtn').addEventListener('click', function() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    document.getElementById('time_out_raw').value = `${hours}:${minutes}`;
    updateTimeOutDisplay(`${hours}:${minutes}`);
});
</script>

<script>
function updateCurrentTime() {
    const timeInField = document.getElementById('time_in');
    if (timeInField) {
        const now = new Date();
        const hours = now.getHours();
        const minutes = now.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const formattedHours = hours % 12 || 12;
        const formattedMinutes = minutes.toString().padStart(2, '0');
        timeInField.value = `${formattedHours}:${formattedMinutes} ${ampm}`;
    }
}

// Update time every second
setInterval(updateCurrentTime, 1000);
// Initial update
updateCurrentTime();
</script>

<script>
function updateTimeOutDisplay(value) {
    if (value) {
        const [hours, minutes] = value.split(':');
        const date = new Date();
        date.setHours(hours);
        date.setMinutes(minutes);
        const formattedTime = date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        document.getElementById('time_out').value = formattedTime;
    } else {
        document.getElementById('time_out').value = '';
    }
    validateTimeAndUpdateButton();
}
</script>

<script>
const studentSearch = document.getElementById('student_search');
const searchResults = document.getElementById('searchResults');
const selectedStudentNumber = document.getElementById('selected_student_number');

studentSearch.addEventListener('input', function() {
    const searchTerm = this.value;
    if (searchTerm.length < 2) {
        searchResults.style.display = 'none';
        return;
    }

    fetch(`attendance.php?search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            searchResults.innerHTML = '';
            if (data.length > 0) {
                data.forEach(student => {
                    const div = document.createElement('div');
                    div.className = 'list-group-item list-group-item-action';
                    div.style.cursor = 'pointer';
                    div.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${student.student_number}</strong> - ${student.name}
                                <br>
                                <small class="text-muted">${student.course}</small>
                            </div>
                        </div>
                    `;
                    div.addEventListener('click', () => {
                        studentSearch.value = `${student.student_number} - ${student.name} (${student.course})`;
                        selectedStudentNumber.value = student.student_number;
                        searchResults.style.display = 'none';
                    });
                    searchResults.appendChild(div);
                });
                searchResults.style.display = 'block';
            } else {
                searchResults.style.display = 'none';
            }
        });
});

// Hide search results when clicking outside
document.addEventListener('click', function(e) {
    if (!studentSearch.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.style.display = 'none';
    }
});
</script>

<style>
#searchResults {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    width: 100%;
}

#searchResults .list-group-item {
    padding: 0.5rem 1rem;
}

#searchResults .list-group-item:hover {
    background-color: #f8f9fa;
}

#searchResults .d-flex {
    gap: 0.5rem;
}

#searchResults small {
    line-height: 1;
}
</style>

<script>
function validateTimeAndUpdateButton() {
    const timeIn = document.getElementById('time_in').value;
    const timeOutRaw = document.getElementById('time_out_raw').value;
    const submitBtn = document.getElementById('form-submit-btn');
    const studentNumber = document.getElementById('selected_student_number').value;
    const timeError = document.getElementById('timeError');
    
    let isValid = true;
    let errorMessage = '';

    // Check if student is selected
    if (!studentNumber) {
        isValid = false;
        errorMessage = 'Please select a student';
    }

    // Check if time out is set
    if (!timeOutRaw) {
        isValid = false;
        errorMessage = errorMessage || 'Please set a time out';
    } else {
        const timeInDate = new Date(`2000-01-01 ${timeIn}`);
        const timeOutDate = new Date(`2000-01-01 ${timeOutRaw}`);
        const currentDate = new Date();
        
        // Reset hours/minutes to compare only times
        currentDate.setFullYear(2000, 0, 1);
        
        if (timeOutDate < timeInDate) {
            isValid = false;
            errorMessage = 'Time out cannot be earlier than time in';
        } else if (timeOutDate < currentDate) {
            isValid = false;
            errorMessage = 'Time out cannot be earlier than current time';
        }
    }

    // Update button state and error message
    submitBtn.disabled = !isValid;
    timeError.textContent = errorMessage;
    timeError.style.display = errorMessage ? 'block' : 'none';
}

// Add event listeners
document.getElementById('time_out_raw').addEventListener('input', function() {
    updateTimeOutDisplay(this.value);
});

document.getElementById('timeOutBtn').addEventListener('click', function() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    document.getElementById('time_out_raw').value = `${hours}:${minutes}`;
    updateTimeOutDisplay(`${hours}:${minutes}`);
});

// Form submission validation
document.querySelector('form').addEventListener('submit', function(e) {
    const studentNumber = document.getElementById('selected_student_number').value;
    const timeOut = document.getElementById('time_out').value;
    const timeOutRaw = document.getElementById('time_out_raw').value;
    
    if (!studentNumber || !timeOutRaw) {
        e.preventDefault();
        alert('Please fill in all required fields');
        return;
    }

    if (timeOut) {
        const timeOutDate = new Date(`2000-01-01 ${timeOut}`);
        const currentDate = new Date();
        const timeInDate = new Date(`2000-01-01 ${document.getElementById('time_in').value}`);
        
        if (timeOutDate < timeInDate) {
            e.preventDefault();
            alert('Time out cannot be earlier than time in');
            return;
        }
        
        if (timeOutDate < currentDate) {
            e.preventDefault();
            alert('Time out cannot be earlier than current time');
            return;
        }
    }
});

// Initial validation
validateTimeAndUpdateButton();

// Add validation when student is selected
document.getElementById('student_search').addEventListener('change', validateTimeAndUpdateButton);
</script>

<!-- Add error message container -->
<div id="timeError" class="text-danger mt-2" style="display: none;"></div>

</body>
</html>
