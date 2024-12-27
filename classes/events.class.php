<?php
require_once 'database.class.php';

class Events {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all events ordered by event date
    public function getAllEvents() {
        try {
            $sql = "SELECT * FROM events ORDER BY event_date ASC";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching events: " . $e->getMessage());
        }
    }

    // Fetch a single event by ID
    public function getEventById($id) {
        try {
            $sql = "SELECT * FROM events WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching event: " . $e->getMessage());
        }
    }

    // Event Logs
    private function logAction($userId, $action, $eventId, $description) {
        try {
            $sql = "INSERT INTO event_logs (user_id, action, event_id, description) 
                    VALUES (:user_id, :action, :event_id, :description)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':action', $action, PDO::PARAM_STR);
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Error logging action: " . $e->getMessage());
        }
    }

    // Add a new event
    public function addEvent($title, $description, $organizers, $starttime, $endtime, $venue, $event_date) {
        try {
            $sql = "INSERT INTO events (title, description, organizers, starttime, endtime, venue, event_date) 
                    VALUES (:title, :description, :organizers, :starttime, :endtime, :venue, :event_date)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':organizers', $organizers);
            $stmt->bindParam(':starttime', $starttime);
            $stmt->bindParam(':endtime', $endtime);
            $stmt->bindParam(':venue', $venue);
            $stmt->bindParam(':event_date', $event_date);
            $stmt->execute();

            $this->logAction($_SESSION['user']['id'], 'add', $id, "Added event: $title");
    
            return true;
        } catch (PDOException $e) {
            die("Error adding event: " . $e->getMessage());
        }
    }

    // Update an existing event
    public function updateEvent($id, $title, $description, $organizers, $starttime, $endtime, $venue, $event_date) {
        try {
            // Step 1: Update the event details
            $sql = "UPDATE events 
                    SET title = :title, description = :description, organizers = :organizers, 
                        starttime = :starttime, endtime = :endtime, venue = :venue, event_date = :event_date 
                    WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':organizers', $organizers);
            $stmt->bindParam(':starttime', $starttime);
            $stmt->bindParam(':endtime', $endtime);
            $stmt->bindParam(':venue', $venue);
            $stmt->bindParam(':event_date', $event_date);
            $stmt->execute();
    
            // Step 2: Log the update action
            $this->logAction(
                $_SESSION['user']['id'],    // User ID
                'update',                   // Action
                $id,                        // Event ID
                "Updated event: $title"     // Description
            );
    
            return true;
        } catch (PDOException $e) {
            die("Error updating event: " . $e->getMessage());
        }
    }
    

    // Delete an event by ID
    public function deleteEvent($id) {
        try {
            // Step 1: Retrieve event details for logging
            $event = $this->getEventById($id); // Fetch the event before deleting
            if (!$event) {
                throw new Exception("Event not found.");
            }
    
            // Step 2: Nullify logs associated with the event
            $sql = "UPDATE event_logs SET event_id = NULL WHERE event_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
    
            // Step 3: Log the deletion action
            $this->logAction(
                $_SESSION['user']['id'],    // User ID
                'delete',                   // Action
                $id,                        // Event ID
                "Deleted event: " . $event['title'] // Description with the event title
            );
    
            // Step 4: Delete the event
            $sql = "DELETE FROM events WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
    
            return true;
        } catch (PDOException $e) {
            die("Error deleting event: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
    
    
    
    
}

// Initialize the Events class
global $pdo;
$eventsClass = new Events($pdo);

// Initialize variables
$title = $description = $organizers = $starttime = $endtime = $venue = $event_date = "";
$title_err = $description_err = $organizers_err = $starttime_err = $endtime_err = $venue_err = $event_date_err = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate title
    if (empty(trim($_POST['title']))) {
        $title_err = "Please enter the event title.";
    } else {
        $title = trim($_POST['title']);
    }

    // Validate description
    if (empty(trim($_POST['description']))) {
        $description_err = "Please enter the event description.";
    } else {
        $description = trim($_POST['description']);
    }

    // Validate organizers
    if (empty(trim($_POST['organizers']))) {
        $organizers_err = "Please enter the organizers.";
    } else {
        $organizers = trim($_POST['organizers']);
    }

    // Validate start time
    if (empty(trim($_POST['starttime']))) {
        $starttime_err = "Please enter the start time.";
    } else {
        $starttime = trim($_POST['starttime']);
    }

    // Validate end time
    if (empty(trim($_POST['endtime']))) {
        $endtime_err = "Please enter the end time.";
    } elseif ($starttime >= trim($_POST['endtime'])) {
        $endtime_err = "End time cannot be earlier than or equal to the start time.";
    } else {
        $endtime = trim($_POST['endtime']);
    }

    // Validate venue
    if (empty(trim($_POST['venue']))) {
        $venue_err = "Please enter the venue.";
    } else {
        $venue = trim($_POST['venue']);
    }

    // Validate event date
    if (empty(trim($_POST['event_date']))) {
        $event_date_err = "Please enter the event date.";
    } else {
        $event_date = trim($_POST['event_date']);
    }

    // If no errors, proceed to add or update event
    if (empty($title_err) && empty($description_err) && empty($organizers_err) && empty($starttime_err) &&
        empty($endtime_err) && empty($venue_err) && empty($event_date_err)) {
        if (!empty($_POST['update_id'])) {
            $eventsClass->updateEvent($_POST['update_id'], $title, $description, $organizers, $starttime, $endtime, $venue, $event_date);
        } else {
            $eventsClass->addEvent($title, $description, $organizers, $starttime, $endtime, $venue, $event_date);
        }
        
         // Check user role and redirect accordingly
         if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'officer') {
            header("Location: events_officer.php");
        } else {
            header("Location: events.php");
        }
        exit();
    }
}

// Handle deletion of an event
if (isset($_SESSION['user']['role'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        // Admin can delete an event and stay on the events page
        if (isset($_GET['delete_id'])) {
            $eventsClass->deleteEvent($_GET['delete_id']);
            header("Location: events.php");
            exit();
        }
    } elseif ($_SESSION['user']['role'] === 'officer') {
        // Officer can delete an event and stay on the events_officer page
        if (isset($_GET['delete_id'])) {
            $eventsClass->deleteEvent($_GET['delete_id']);
            header("Location: events_officer.php");
            exit();
        }
    }
}



// Fetch all events
$events = $eventsClass->getAllEvents();
?>
