<?php
require '../classes/account.class.php';
require '../classes/database.class.php';

session_start();

// Redirect if the user is not an admin
Account::redirect_if_not_logged_in('admin');

require '../classes/students.class.php';
require '../tools/functions.php';

?>

<!DOCTYPE html>
<html lang="en">
<?php include '../includes/_head.php'; ?>
<link href="../css/dashboard.css" rel="stylesheet">
<link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<body>
<?php include '../includes/_topnav.php'; ?>
<?php include 'modals_admin/add_student_modal.php'; ?>
<?php include 'modals_admin/edit_student_modal.php'; ?>

<div class="container mt-4">
    <h1>Manage Students</h1>

    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addStudentModal">
    Add Student
    </button>

    <h2>Student List</h2>
    <div class="table-responsive">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Student Number</th>
                <th>Name</th>
                <th>Course</th>
                <th>Year Level</th>
                <th>Section</th>
                <th>Birthdate</th>
                <th>Sex</th>
                <th>Address</th>
                <th>WMSU Email</th>
                <th>Personal Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['student_number']) ?></td>
                    <td><?= htmlspecialchars($student['last_name']) ?>, <?= htmlspecialchars($student['first_name']) ?> <?= htmlspecialchars($student['middle_name']) ?></td>
                    <td><?= htmlspecialchars($student['course']) ?></td>
                    <td><?= htmlspecialchars($student['year_level']) ?></td>
                    <td><?= htmlspecialchars($student['section']) ?></td>
                    <td><?= htmlspecialchars($student['birthdate']) ?></td>
                    <td><?= htmlspecialchars($student['sex']) ?></td>
                    <td><?= htmlspecialchars($student['address']) ?></td>
                    <td><?= htmlspecialchars($student['wmsu_email']) ?></td>
                    <td><?= htmlspecialchars($student['personal_email']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-btn" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editStudentModal"
                                data-id="<?= $student['id'] ?>" 
                                data-student_number="<?= htmlspecialchars($student['student_number']) ?>" 
                                data-last_name="<?= htmlspecialchars($student['last_name']) ?>" 
                                data-first_name="<?= htmlspecialchars($student['first_name']) ?>" 
                                data-middle_name="<?= htmlspecialchars($student['middle_name']) ?>" 
                                data-course="<?= htmlspecialchars($student['course']) ?>" 
                                data-year_level="<?= htmlspecialchars($student['year_level']) ?>"
                                data-section="<?= htmlspecialchars($student['section']) ?>"
                                data-birthdate="<?= htmlspecialchars($student['birthdate']) ?>"
                                data-sex="<?= htmlspecialchars($student['sex']) ?>"
                                data-address="<?= htmlspecialchars($student['address']) ?>"
                                data-wmsu_email="<?= htmlspecialchars($student['wmsu_email']) ?>"
                                data-personal_email="<?= htmlspecialchars($student['personal_email']) ?>"
                                data-status="<?= htmlspecialchars($student['status']) ?>">
                            Edit
                        </button>
                        
                        <a href="students.php?delete_id=<?= $student['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    
</div>

<footer class="text-center py-3 mt-5">
    <p class="mb-0">&copy; 2024 Event Management System. All Rights Reserved.</p>
</footer>
<script src="../vendor/jquery/jquery.min.js"></script>
<!-- <script src="../js/bootstrap.bundle.min.js"></script> -->

<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#dataTable').DataTable();  // Ensure this ID matches your table ID
});
</script>

<?php if (!empty($student_number_err) || !empty($last_name_err) || !empty($first_name_err) ||
          !empty($middle_name_err) || !empty($course_err) || !empty($year_level_err) ||
          !empty($section_err) || !empty($birthdate_err) || !empty($sex_err) || !empty($address_err) ||
          !empty($wmsu_email_err) || !empty($personal_email_err) || !empty($status_err)): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var addStudentModal = new bootstrap.Modal(document.getElementById('addStudentModal'));
      addStudentModal.show();
    });
  </script>
<?php endif; ?>
<script>
document.getElementById('closeModalBtn').addEventListener('click', function () {
    // Reset the form fields
    document.getElementById('studentForm').reset();

    // Clear any error messages (assuming error messages have a common class like 'error-message')
    document.querySelectorAll('.error-message').forEach(function (errorElement) {
        errorElement.textContent = '';
    });
});
</script>
<script>
document.querySelectorAll('.edit-btn').forEach(button => {
  button.addEventListener('click', function () {
    document.getElementById('update_id').value = this.dataset.id;
    document.getElementById('modal-student_number').value = this.dataset.student_number;
    document.getElementById('modal-last_name').value = this.dataset.last_name;
    document.getElementById('modal-first_name').value = this.dataset.first_name;
    document.getElementById('modal-middle_name').value = this.dataset.middle_name;
    document.getElementById('modal-course').value = this.dataset.course;
    document.getElementById('modal-year_level').value = this.dataset.year_level;
    document.getElementById('modal-section').value = this.dataset.section;
    document.getElementById('modal-birthdate').value = this.dataset.birthdate;
    document.getElementById('modal-sex').value = this.dataset.sex;
    document.getElementById('modal-address').value = this.dataset.address;
    document.getElementById('modal-wmsu_email').value = this.dataset.wmsu_email;
    document.getElementById('modal-personal_email').value = this.dataset.personal_email;
    document.getElementById('modal-status').value = this.dataset.status;
  });
});

</script>
</body>
</html>

