<?php
require '../classes/account.class.php';
require '../classes/database.class.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if the user is not an admin
Account::redirect_if_not_logged_in('admin');

require '../classes/events.class.php';
require '../tools/functions.php';
?>


<!DOCTYPE html>
<html lang="en">
<?php include '../includes/_head.php'; ?>
<link href="../css/dashboard.css" rel="stylesheet">
<link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<body>
<?php include '../includes/_topnav.php'; ?>
<?php include 'modals_admin/edit_event_modal.php'; ?>
<?php include 'modals_admin/add_event_modal.php'; ?>

<div class="container mt-4">
    <h1>Manage Events</h1>

    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addEventModal">
    Add Event
    </button>


    <h2>Event List</h2>
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Organizers</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Venue</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['title']) ?></td>
                    <td><?= htmlspecialchars($event['description']) ?></td>
                    <td><?= htmlspecialchars($event['organizers']) ?></td>
                    <td><?= htmlspecialchars($event['starttime']) ?></td>
                    <td><?= htmlspecialchars($event['endtime']) ?></td>
                    <td><?= htmlspecialchars($event['venue']) ?></td>
                    <td><?= htmlspecialchars($event['event_date']) ?></td>
                    <td>
                    <button class="btn btn-sm btn-warning edit-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editEventModal"
                            data-id="<?= htmlspecialchars($event['id']) ?>"
                            data-title="<?= htmlspecialchars($event['title']) ?>"
                            data-description="<?= htmlspecialchars($event['description']) ?>"
                            data-organizers="<?= htmlspecialchars($event['organizers']) ?>"
                            data-starttime="<?= htmlspecialchars($event['starttime']) ?>"
                            data-endtime="<?= htmlspecialchars($event['endtime']) ?>"
                            data-venue="<?= htmlspecialchars($event['venue']) ?>"
                            data-event_date="<?= htmlspecialchars($event['event_date']) ?>">
                    Edit
                    </button>
                        <a href="events.php?delete_id=<?= $event['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
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
<!-- <script src="../js/bootstrap.bundle.min.js"></script> -->
<?php if (!empty($title_err) || !empty($description_err) || !empty($organizers_err) ||
          !empty($starttime_err) || !empty($endtime_err) || !empty($venue_err) || !empty($event_date_err)): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var addEventModal = new bootstrap.Modal(document.getElementById('addEventModal'));
      addEventModal.show();
    });
  </script>
<?php endif; ?>

<script>
document.getElementById('closeModalBtn').addEventListener('click', function () {
    // Reset the form fields
    document.getElementById('eventForm').reset();

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
      document.getElementById('modal-title').value = this.dataset.title;
      document.getElementById('modal-description').value = this.dataset.description;
      document.getElementById('modal-organizers').value = this.dataset.organizers;
      document.getElementById('modal-starttime').value = this.dataset.starttime;
      document.getElementById('modal-endtime').value = this.dataset.endtime;
      document.getElementById('modal-venue').value = this.dataset.venue;
      document.getElementById('modal-event_date').value = this.dataset.event_date;
    });
  });
</script>



<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();  // Ensure this ID matches your table ID
    });
</script>
</body>
</html>
