<?php

global $pdo;

// Initialize variables
$username = $password = $role = "";
$username_err = $password_err = $role_err = "";

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate username
    if (empty(trim($_POST['username']))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST['username']);

        // Check for duplicate username
        $sql = "SELECT id FROM users WHERE username = :username";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $username_err = "This username is already taken.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = "Please enter a password.";
    } else {
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password
    }

    // Validate role
    if (empty(trim($_POST['role'] ?? ''))) {
        $role_err = "Please enter the role.";
    } else {
        $role = trim($_POST['role']);
    }

    // Check input errors before inserting or updating in the database
    if (empty($username_err) && empty($password_err) && empty($role_err)) {
        if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
            // Update existing user
            $sql = "UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":password", $password);
                $stmt->bindParam(":role", $role);
                $stmt->bindParam(":id", $_POST['update_id']);
                if ($stmt->execute()) {
                    header("Location: users.php");
                    exit();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        } else {
            // Insert new user
            $sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":password", $password);
                $stmt->bindParam(":role", $role);
                if ($stmt->execute()) {
                    header("Location: users.php");
                    exit();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        }
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $sql = "DELETE FROM users WHERE id = :id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $_GET['delete_id']);
        if ($stmt->execute()) {
            header("Location: users.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
}

// Fetch all users
$sql = "SELECT * FROM users ORDER BY username ASC";
$users = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);