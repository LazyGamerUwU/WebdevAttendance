<?php
require '../classes/database.class.php';

session_start();

// Ensure only admins can access
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

global $pdo;

$sql = "SELECT student_logs.*, users.username 
        FROM student_logs 
        JOIN users ON student_logs.user_id = users.id 
        ORDER BY student_logs.timestamp DESC";

$logs = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../includes/_head.php'; ?>
<link href="../css/dashboard.css" rel="stylesheet">
<link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<body>
    <?php include '../includes/_topnav.php'; ?>
    <div class="container mt-4">
        <h1>Student Logs</h1>
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Student ID</th>
                    <th>Description</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['username']) ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['student_id']) ?></td>
                        <td><?= htmlspecialchars($log['description']) ?></td>
                        <td><?= htmlspecialchars($log['timestamp']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
</body>
</html>
