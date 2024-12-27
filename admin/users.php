<?php
require '../classes/account.class.php';
require '../classes/database.class.php';
require '../classes/users.class.php';
require '../tools/functions.php';
session_start();

// Redirect if the user is not an admin
Account::redirect_if_not_logged_in('admin');
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../includes/_head.php'; ?>
<link href="../css/dashboard.css" rel="stylesheet">
<link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<body>
<?php include '../includes/_topnav.php'; ?>
<?php include 'modals_admin/add_user_modal.php'; ?>
<?php include 'modals_admin/edit_user_modal.php'; ?>


<div class="container mt-4">
    <h1>Manage Users</h1>

    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
    Add User
    </button>

   

    <h2>User List</h2>
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-btn"
                                data-bs-toggle="modal"  
                                data-bs-target="#editUserModal"
                                data-id="<?= $user['id'] ?>" 
                                data-username="<?= htmlspecialchars($user['username']) ?>" 
                                data-role="<?= htmlspecialchars($user['role']) ?>">
                            Edit
                        </button>

                        <a href="users.php?delete_id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<footer class="text-center py-3 mt-5">
    <p class="mb-0">&copy; 2024 Event Management System. All Rights Reserved.</p>
</footer>
<script src="../vendor/jquery/jquery.min.js"></script>

<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Data Table -->
<script>
$(document).ready(function() {
	$('#dataTable').DataTable(); 
});
</script>

<!-- Add User Modal -->
<?php if (!empty($username_err) || !empty($password_err) || !empty($role_err)): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
      addUserModal.show();
    });
  </script>
<?php endif; ?>


<!-- Close Button to remove error messages -->
<script>
document.getElementById('closeModalBtn').addEventListener('click', function () {
    // Reset the form fields
    document.getElementById('userForm').reset();

    // Clear any error messages (assuming error messages have a common class like 'error-message')
    document.querySelectorAll('.error-message').forEach(function (errorElement) {
        errorElement.textContent = '';
    });
});
</script>


<!-- For Edit User Modal -->
<script>
document.querySelectorAll('.edit-btn').forEach(button => {
  button.addEventListener('click', function () {
    document.getElementById('update_id').value = this.dataset.id;
    document.getElementById('modal-username').value = this.dataset.username;
    document.getElementById('modal-password').value = this.dataset.password;
    document.getElementById('modal-role').value = this.dataset.role;
  });
});

</script>

</body>
</html>
