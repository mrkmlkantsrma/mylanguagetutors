<?php
require_once __DIR__ . '/../config/Database.php';

class Booking
{
    private $conn;

    // Object properties
    public $id;
    public $username;
    public $tutor_username;
    public $date_booked;
    public $class_date_time;
    public $duration;
    public $zoom_link;
    public $status;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function updateAvailability($tutorId, $availability, $username)
    {
        // Check if the tutorId exists
        $checkQuery = "SELECT * FROM availability WHERE tutor_id = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->execute([$tutorId]);
        $exists = $checkStmt->fetch(PDO::FETCH_ASSOC);

        // Start a transaction
        $this->conn->beginTransaction();

        try {
            if (!$exists) {
                // If the tutorId does not exist, insert a new row
                $insertQuery = "INSERT INTO availability (tutor_id, mon, tue, wed, thu, fri, sat, sun, username) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $insertStmt = $this->conn->prepare($insertQuery);
                $insertStmt->execute([
                    $tutorId,
                    json_encode($availability['mon'] ?? []),
                    json_encode($availability['tue'] ?? []),
                    json_encode($availability['wed'] ?? []),
                    json_encode($availability['thu'] ?? []),
                    json_encode($availability['fri'] ?? []),
                    json_encode($availability['sat'] ?? []),
                    json_encode($availability['sun'] ?? []),
                    $username,
                ]);
            } else {
                // If the tutorId exists, update the row
                $query = "UPDATE availability SET mon = ?, tue = ?, wed = ?, thu = ?, fri = ?, sat = ?, sun = ?, username = ? WHERE tutor_id = ?";
                $stmt = $this->conn->prepare($query);

                // Execute the statement
                $stmt->execute([
                    json_encode($availability['mon'] ?? []),
                    json_encode($availability['tue'] ?? []),
                    json_encode($availability['wed'] ?? []),
                    json_encode($availability['thu'] ?? []),
                    json_encode($availability['fri'] ?? []),
                    json_encode($availability['sat'] ?? []),
                    json_encode($availability['sun'] ?? []),
                    $username,
                    $tutorId,
                ]);
            }

            // Commit the transaction
            $this->conn->commit();
        } catch (Exception $e) {
            // Rollback in case there was an error
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function getAvailability($tutorId)
    {
        try {
            $query = "SELECT mon, tue, wed, thu, fri, sat, sun FROM availability WHERE tutor_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$tutorId]);
            $results = $stmt->fetch(PDO::FETCH_ASSOC);

            // If there's no availability data for the provided tutorId, return an empty array
            if (!$results) {
                return ['mon' => [], 'tue' => [], 'wed' => [], 'thu' => [], 'fri' => [], 'sat' => [], 'sun' => []];
            }

            // Decode the JSON encoded hours into an array
            foreach ($results as $day => $hours) {
                if (empty($hours)) {
                    $results[$day] = [];
                } else {
                    $results[$day] = json_decode($hours, true); // True parameter to return array
                }
            }

            return $results;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAvailabilityByUsername($username)
    {
        try {
            // Prepare the query to get the availability by the username
            $query = "SELECT mon, tue, wed, thu, fri, sat, sun FROM availability WHERE username = ?";
            $stmt = $this->conn->prepare($query);

            // Execute the statement
            $stmt->execute([$username]);

            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the query returned a result
            if ($result === false) {
                error_log("Query did not return a result for username: " . $username);
                throw new Exception("No availability data found for username: " . $username);
            }

            // Decode the JSON encoded hours into an array
            foreach ($result as $day => $hours) {
                if (empty($hours)) {
                    $result[$day] = [];
                } else {
                    $result[$day] = json_decode($hours, true); // True parameter to return array
                }
            }

            // Return the availability
            return $result;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateTimezone($tutorId, $timezone)
    {
        // Check if the tutorId exists
        $checkQuery = "SELECT * FROM availability WHERE tutor_id = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->execute([$tutorId]);
        $exists = $checkStmt->fetch(PDO::FETCH_ASSOC);

        // Start a transaction
        $this->conn->beginTransaction();

        try {
            if (!$exists) {
                // If the tutorId does not exist, insert a new row
                $insertQuery = "INSERT INTO availability (tutor_id, timezone) VALUES (?, ?)";
                $insertStmt = $this->conn->prepare($insertQuery);
                $insertStmt->execute([$tutorId, $timezone]);
            } else {
                // If the tutorId exists, update the row
                $query = "UPDATE availability SET timezone = ? WHERE tutor_id = ?";
                $stmt = $this->conn->prepare($query);

                // Execute the statement
                $stmt->execute([$timezone, $tutorId]);
            }

            // Commit the transaction
            $this->conn->commit();
        } catch (Exception $e) {
            // Rollback in case there was an error
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function getTimezone($tutorId)
    {
        try {
            // Prepare the query to get the timezone
            $query = "SELECT timezone FROM availability WHERE tutor_id = ?";
            $stmt = $this->conn->prepare($query);

            // Execute the statement
            $stmt->execute([$tutorId]);

            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Return the timezone
            return $result['timezone'];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getTutorTimezoneByUsername($username)
    {
        try {
            // Prepare the query to get the timezone
            $query = "SELECT timezone FROM availability WHERE username = ?";
            $stmt = $this->conn->prepare($query);

            // Execute the statement
            $stmt->execute([$username]);

            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Return the timezone
            return $result['timezone'];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    // Method to create new booking
    public function bookSlot($username, $tutorUsername, $dateTime, $zoomLink, $language)
    {
        // Convert the date and time string into a DateTime object
        $dateTimeObj = new DateTime($dateTime);

        $this->username = $username;
        $this->tutor_username = $tutorUsername;
        $this->date_booked = date("Y-m-d H:i:s"); // The current date and time
        $this->class_date_time = $dateTimeObj->format("Y-m-d H:i:s"); // The date and time of the class
        $this->duration = 60; // The duration of the class (in hours)
        $this->zoom_link = $zoomLink;
        $this->status = 'booked'; // The status of the booking
        $this->language = $language;

        // Use the create method to insert the booking into the database
        if ($this->create()) {
            return true;
        } else {
            throw new Exception("There was an error in booking the slot.");
        }
    }

    public function incrementClassesUsed($username)
    {
        // Prepare the SQL statement
        $stmt = $this->conn->prepare("UPDATE subscriptions SET classes_used = classes_used + 1 WHERE username = :username");

        // Bind the parameters
        $stmt->bindParam(':username', $username);

        // Execute the statement
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("There was an error updating the classes used.");
        }
    }

    private function create()
    {
        $query =
            "INSERT INTO bookings(username, tutor_username, date_booked, class_date_time, duration, zoom_link, status, language) VALUES (:username, :tutor_username, :date_booked, :class_date_time, :duration, :zoom_link, :status, :language)";
        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":tutor_username", $this->tutor_username);
        $stmt->bindParam(":date_booked", $this->date_booked);
        $stmt->bindParam(":class_date_time", $this->class_date_time);
        $stmt->bindParam(":duration", $this->duration);
        $stmt->bindParam(":zoom_link", $this->zoom_link);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":language", $this->language);

        // Execute the query
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Method to check if user has classes left
    public function hasClassesLeft($username)
    {
        $query = "SELECT classes_used, number_of_classes FROM subscriptions WHERE username = ? AND number_of_classes > classes_used ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? true : false;
    }

    public function getBookedSlots($tutor_username)
    {
        try {
            $query = "SELECT class_date_time FROM bookings WHERE tutor_username = ? AND status = 'booked'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$tutor_username]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $bookedSlots = [];
            foreach ($results as $result) {
                $date = new DateTime($result["class_date_time"]);
                $bookedSlots[] = $date->format('Y-m-d\TH:i:s');
            }

            return $bookedSlots;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
?>
