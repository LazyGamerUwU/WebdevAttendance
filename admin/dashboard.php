<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require '../classes/account.class.php';
Account::redirect_if_not_logged_in('admin');
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../includes/_head.php'; ?>
<link href="../css/dashboard-main.css" rel="stylesheet">
<body>
<?php include '../includes/_topnav.php'; ?>
<div class="container">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user']['username']); ?>!</p>
    <nav class="navbar navbar-expand-lg" style="background-color: #d3d3d3 !important; text-align: center;">
    <a class="navbar-brand mx-auto" href="../admin/events.php">
        <span class="svg-icon calendar"></span>
            Event Manager
    </a>
    </nav>
    <nav class="navbar navbar-expand-lg" style="background-color: #d3d3d3 !important; text-align: center;">
        <a class="navbar-brand mx-auto" href="../admin/attendance.php">
        <span class="svg-icon clipboard"></span>
            Attendance
        </a>
    </nav>
    <nav class="navbar navbar-expand-lg" style="background-color: #d3d3d3 !important; text-align: center;">
        <a class="navbar-brand mx-auto" href="../admin/students.php">
            <span class="svg-icon graduation-cap"></span>
                Students
        </a>
    </nav>
    <nav class="navbar navbar-expand-lg" style="background-color: #d3d3d3 !important; text-align: center;">
        <a class="navbar-brand mx-auto" href="../admin/users.php">
            <span class="svg-icon users"></span>
                Users
        </a>
    </nav>
</div>
</body>
<footer class="text-center py-3 mt-5">
    <p class="mb-0">&copy; 2024 Event Management System. All Rights Reserved.</p>
</footer>
</html>
