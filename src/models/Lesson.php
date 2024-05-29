<?php
require_once __DIR__ . '/../config/Database.php';

class Lesson
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function getBookedLessonsByTutor($tutor_username, $offset, $lessonsPerPage)
    {
        // Prepare the SQL query
        $query = "SELECT *, username, zoom_link FROM bookings WHERE tutor_username = :tutor_username AND status = 'Booked' ORDER BY class_date_time ASC LIMIT :offset, :lessonsPerPage";
        $stmt = $this->conn->prepare($query);

        // Bind the value
        $stmt->bindParam(":tutor_username", $tutor_username);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindParam(":lessonsPerPage", $lessonsPerPage, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Return the result
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookedLessonsCount($tutor_username)
    {
        // Prepare the SQL query
        $query = "SELECT COUNT(*) as total FROM bookings WHERE tutor_username = :tutor_username AND status = 'Booked'";
        $stmt = $this->conn->prepare($query);

        // Bind the value
        $stmt->bindParam(":tutor_username", $tutor_username);

        // Execute the query
        $stmt->execute();

        // Return the result
        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }

    public function updateLessonStatus($lessonId, $newStatus)
    {
        // Prepare the SQL query
        $query = "UPDATE bookings SET status = :new_status WHERE id = :lesson_id";
        $stmt = $this->conn->prepare($query);

        // Bind the values
        $stmt->bindParam(":new_status", $newStatus);
        $stmt->bindParam(":lesson_id", $lessonId);

        // Execute the query
        $stmt->execute();
    }

    public function saveEarnings($tutorUsername, $amount)
    {
        $query = "SELECT * FROM total_available_earning WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $tutorUsername);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $query = "UPDATE total_available_earning SET 
                    total_earned = total_earned + :amount,
                    available = available + :amount
                    WHERE username = :username";
                    
        } else {
            $query = "INSERT INTO total_available_earning (username, total_earned, available)
                    VALUES (:username, :amount, :amount)";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $tutorUsername);
        $stmt->bindParam(":amount", $amount);
        
        $stmt->execute();
    }

    public function getLessonHistory($tutor_username, $offset, $lessonsPerPage)
    {
        // Prepare the SQL query
        $query = "SELECT * FROM bookings WHERE tutor_username = :tutor_username AND (status = 'SUBMITTED' OR status = 'COMPLETED' OR status = 'CANCELLED') ORDER BY class_date_time DESC LIMIT :offset, :lessonsPerPage";
        $stmt = $this->conn->prepare($query);

        // Bind the value
        $stmt->bindParam(":tutor_username", $tutor_username);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindParam(":lessonsPerPage", $lessonsPerPage, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Return the result
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLessonHistoryCount($tutor_username)
    {
        // Prepare the SQL query
        $query = "SELECT COUNT(*) as total FROM bookings WHERE tutor_username = :tutor_username AND (status = 'SUBMITTED' OR status = 'COMPLETED' OR status = 'CANCELLED')";
        $stmt = $this->conn->prepare($query);

        // Bind the value
        $stmt->bindParam(":tutor_username", $tutor_username);

        // Execute the query
        $stmt->execute();

        // Return the result
        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }

    // student Booking

    public function getStudentBookings($username, $offset, $lessonsPerPage)
    {
        // Prepare the SQL query
        $query = "SELECT * FROM bookings WHERE username = :username AND status = 'Booked' ORDER BY class_date_time ASC LIMIT :offset, :lessonsPerPage";
        $stmt = $this->conn->prepare($query);

        // Bind the value
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindParam(":lessonsPerPage", $lessonsPerPage, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Return the result
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookingInfo($bookingId) {
        $query = "SELECT username, date_booked FROM bookings WHERE id = :booking_id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":booking_id", $bookingId, PDO::PARAM_INT);
        
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } 
        
        return NULL;
    }

    public function checkPendingRequest($bookingId)
    {
        // Order by new_date_time_request_date in descending order and limit to 1 record to get the latest record
        $query = "SELECT change_request_id, booking_id, status FROM booking_changes ORDER BY new_date_time_request_date DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        // Fetch the result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if there is a result and if its booking_id matches the provided bookingId and its status is 'pending'
        if ($row && $row['booking_id'] == $bookingId && $row['status'] == 'pending') {
            return true;
        }

        // Otherwise, return false
        return false;
    }

    public function getStudentEmailByBookingId($bookingId) {
        // First, get the username from the bookings table
        $query1 = "SELECT username FROM bookings WHERE id = :booking_id LIMIT 1";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bindParam(":booking_id", $bookingId, PDO::PARAM_INT);

        if ($stmt1->execute() && $stmt1->rowCount() > 0) {
            $result = $stmt1->fetch(PDO::FETCH_ASSOC);
            $username = $result['username'];

            // Now, use the username to get the email from the all_users table
            $query2 = "SELECT email FROM all_users WHERE username = :username LIMIT 1";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(":username", $username, PDO::PARAM_STR);

            if ($stmt2->execute() && $stmt2->rowCount() > 0) {
                return $stmt2->fetch(PDO::FETCH_ASSOC)['email'];
            }
        } 

        return NULL;
    }


    public function getTutorEmailByBookingId($bookingId) {
        // First, get the tutor_username from the bookings table
        $query1 = "SELECT tutor_username FROM bookings WHERE id = :booking_id LIMIT 1";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bindParam(":booking_id", $bookingId, PDO::PARAM_INT);

        if ($stmt1->execute() && $stmt1->rowCount() > 0) {
            $result = $stmt1->fetch(PDO::FETCH_ASSOC);
            $tutorUsername = $result['tutor_username'];

            // Now, use the tutor_username to get the email from the all_users table
            $query2 = "SELECT email FROM all_users WHERE username = :username LIMIT 1";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(":username", $tutorUsername, PDO::PARAM_STR);

            if ($stmt2->execute() && $stmt2->rowCount() > 0) {
                return $stmt2->fetch(PDO::FETCH_ASSOC)['email'];
            }
        } 

        return NULL;
    }

    public function getRequesterEmailByBookingId($bookingId) {
        // First, get the requester_username from the booking_changes table
        $query1 = "SELECT requested_by FROM booking_changes WHERE booking_id = :booking_id LIMIT 1";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bindParam(":booking_id", $bookingId, PDO::PARAM_INT);
    
        if ($stmt1->execute() && $stmt1->rowCount() > 0) {
            $result = $stmt1->fetch(PDO::FETCH_ASSOC);
            $requesterUsername = $result['requested_by'];
    
            // Now, use the requester_username to get the email from the all_users table
            $query2 = "SELECT email FROM all_users WHERE username = :username LIMIT 1";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(":username", $requesterUsername, PDO::PARAM_STR);
    
            if ($stmt2->execute() && $stmt2->rowCount() > 0) {
                return $stmt2->fetch(PDO::FETCH_ASSOC)['email'];
            }
        } 
    
        return NULL;
    }

    public function getEmailsByLessonId($lessonId) {
        // Get both username and tutor_username from the bookings table
        $query1 = "SELECT username, tutor_username FROM bookings WHERE id = :id LIMIT 1";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bindParam(":id", $lessonId, PDO::PARAM_INT);
    
        if ($stmt1->execute() && $stmt1->rowCount() > 0) {
            $result = $stmt1->fetch(PDO::FETCH_ASSOC);
            $username = $result['username'];
            $tutorUsername = $result['tutor_username'];
    
            // Now, get the email for the username
            $query2 = "SELECT email FROM all_users WHERE username = :username LIMIT 1";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(":username", $username, PDO::PARAM_STR);
            $studentEmail = ($stmt2->execute() && $stmt2->rowCount() > 0) 
                            ? $stmt2->fetch(PDO::FETCH_ASSOC)['email'] 
                            : null;
    
            // Next, get the email for the tutor_username
            $query3 = "SELECT email FROM all_users WHERE username = :tutor_username LIMIT 1";
            $stmt3 = $this->conn->prepare($query3);
            $stmt3->bindParam(":tutor_username", $tutorUsername, PDO::PARAM_STR);
            $tutorEmail = ($stmt3->execute() && $stmt3->rowCount() > 0) 
                            ? $stmt3->fetch(PDO::FETCH_ASSOC)['email'] 
                            : null;
    
            return [$studentEmail, $tutorEmail];
        } 
    
        return [null, null];
    }
 
    public function saveExtensionRequest($bookingId, $newDateTimeRequest, $newDateTimeRequestReason, $username)
    {
        $query = "INSERT INTO booking_changes (booking_id, requested_by, new_date_time_request, new_date_time_request_reason, new_date_time_request_date, status) 
                VALUES (:booking_id, :requested_by, :new_date_time_request, :new_date_time_request_reason, NOW(), 'pending')";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":booking_id", $bookingId);
        $stmt->bindParam(":new_date_time_request", $newDateTimeRequest);
        $stmt->bindParam(":new_date_time_request_reason", $newDateTimeRequestReason);
        $stmt->bindParam(":requested_by", $username); // Binding the requested_by parameter

        return $stmt->execute();
    }

    public function getLatestRequestStatus($bookingId) {
        $query = "SELECT status, requested_by FROM booking_changes WHERE booking_id = :booking_id ORDER BY new_date_time_request_date DESC LIMIT 1";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":booking_id", $bookingId);
    
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } 
    
        return NULL; 
    }

    public function getAllRequestedChanges($bookingId) {
        $query = "SELECT * FROM booking_changes WHERE booking_id = :booking_id ORDER BY new_date_time_request_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":booking_id", $bookingId, PDO::PARAM_INT);
        
        // Execute the query
        $stmt->execute();
    
        // Return the result
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function recordRequestDecision($bookingId, $status, $username) {
        $query = "INSERT INTO booking_changes (booking_id, requested_by, status, new_date_time_request_date) 
                    VALUES (:booking_id, :requested_by, :status, NOW())";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":booking_id", $bookingId);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":requested_by", $username);
    
        return $stmt->execute();
    }
    
    public function approveRequest($bookingId, $newDateTimeRequest) {
        $query = "UPDATE bookings SET class_date_time = :new_date_time_request WHERE id = :booking_id";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":booking_id", $bookingId);
        $stmt->bindParam(":new_date_time_request", $newDateTimeRequest);
    
        return $stmt->execute();
    }
    
    public function getStudentBookingsCount($username)
    {
        // Prepare the SQL query
        $query = "SELECT COUNT(*) as total FROM bookings WHERE username = :username AND status = 'Booked'";
        $stmt = $this->conn->prepare($query);

        // Bind the value
        $stmt->bindParam(":username", $username);

        // Execute the query
        $stmt->execute();

        // Return the result
        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }

    // STUDENT HISTORY

    public function getStudentLessonHistory($student_username, $offset, $lessonsPerPage)
    {
        // Prepare the SQL query
        $query = "SELECT * FROM bookings WHERE username = :student_username AND (status = 'SUBMITTED' OR status = 'COMPLETED' OR status = 'CANCELLED') ORDER BY class_date_time DESC LIMIT :offset, :lessonsPerPage";
        $stmt = $this->conn->prepare($query);

        // Bind the value
        $stmt->bindParam(":student_username", $student_username);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindParam(":lessonsPerPage", $lessonsPerPage, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Return the result
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentLessonHistoryCount($student_username)
    {
        // Prepare the SQL query
        $query = "SELECT COUNT(*) as total FROM bookings WHERE username = :student_username AND (status = 'SUBMITTED' OR status = 'COMPLETED' OR status = 'CANCELLED')";
        $stmt = $this->conn->prepare($query);

        // Bind the value
        $stmt->bindParam(":student_username", $student_username);

        // Execute the query
        $stmt->execute();

        // Return the result
        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }

    // Get all lessons for lesson reminder

    public function getUpcomingLessonsWithEmails($hoursAhead = 24) {
        // Calculate the current time and the time "hoursAhead" into the future
        $currentTime = date('Y-m-d H:i:s');  // Current server time
        $futureTime = date('Y-m-d H:i:s', strtotime("+{$hoursAhead} hours"));
        
        // Prepare the SQL query
        $query = "
            SELECT b.id, b.username AS student_username, b.tutor_username, b.date_booked, b.class_date_time, 
                   s.email AS student_email, t.email AS tutor_email
            FROM bookings AS b
            JOIN all_users AS s ON b.username = s.username
            JOIN all_users AS t ON b.tutor_username = t.username
            WHERE b.class_date_time BETWEEN :currentTime AND :futureTime
            AND b.status = 'Booked'
            ORDER BY b.class_date_time ASC
        ";
        
        $stmt = $this->conn->prepare($query);
    
        // Bind the values
        $stmt->bindParam(":currentTime", $currentTime);
        $stmt->bindParam(":futureTime", $futureTime);
    
        // Execute the query
        $stmt->execute();
    
        // Return the result
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    // Add a method to update the review and star rating of a lesson
    public function updateLessonReview($lesson_id, $starRating, $review)
    {
        // Get the current date in 'YYYY-MM-DD' format
        $currentDate = date('Y-m-d');

        // Prepare the SQL query
        $query = "UPDATE bookings SET star_rating = :star_rating, review = :review, review_date = :review_date WHERE id = :lesson_id";
        $stmt = $this->conn->prepare($query);

        // Bind the values
        $stmt->bindParam(":star_rating", $starRating);
        $stmt->bindParam(":review", $review);
        $stmt->bindParam(":review_date", $currentDate);
        $stmt->bindParam(":lesson_id", $lesson_id);

        // Execute the query
        $stmt->execute();
    }


    public function saveEarningData($earningDate, $activity, $studentUsername, $tutorUsername, $amount)
    {
        $query = "INSERT INTO earning (date, activity, student, tutor, amount) 
                  VALUES (:earningDate, :activity, :studentUsername, :tutorUsername, :amount)";
        $stmt = $this->conn->prepare($query);

        // Bind the values
        $stmt->bindParam(":earningDate", $earningDate);
        $stmt->bindParam(":activity", $activity);
        $stmt->bindParam(":studentUsername", $studentUsername);
        $stmt->bindParam(":tutorUsername", $tutorUsername);
        $stmt->bindParam(":amount", $amount);

        // Execute the query
        $stmt->execute();
    }

    // Eearning

    public function getEarningDataByTutor($tutorUsername)
    {
        $query = "SELECT * FROM earning WHERE tutor = :tutorUsername";
        $stmt = $this->conn->prepare($query);

        // Bind the values
        $stmt->bindParam(":tutorUsername", $tutorUsername);

        // Execute the query
        $stmt->execute();

        // Fetch all rows
        $earningData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $earningData;
    }

    public function getWithdrawalDataByTutor($tutorUsername) {
        $query = "SELECT response_date AS date, 'Withdrawal' AS activity, requested_amount AS amount 
                  FROM withdrawal_requests 
                  WHERE username = :tutorUsername AND withdrawal_status = 'APPROVED'";
        $stmt = $this->conn->prepare($query);
    
        // Bind the values
        $stmt->bindParam(":tutorUsername", $tutorUsername);
    
        // Execute the query
        $stmt->execute();
    
        // Fetch all rows
        $withdrawalData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $withdrawalData;
    }
    

    public function getEarningsByUsername($username)
    {
        $query = "SELECT total_earned, available FROM total_available_earning WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC); 
        } else {
            // return zero values if the user has no earnings yet
            return array("total_earned" => 0, "available" => 0);
        }
    }

    // Withdrawal email
    public function saveWithdrawalEmail($username, $email) {
    // Check if a record for this username already exists
    $query = "SELECT * FROM withdrawal_emails WHERE username = :username";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // If the record exists, update the email
        $query = "UPDATE withdrawal_emails SET email = :email WHERE username = :username";
    } else {
        // If the record doesn't exist, insert a new one
        $query = "INSERT INTO withdrawal_emails (username, email) VALUES (:username, :email)";
    }

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $email);

    // Execute the query
    return $stmt->execute();
}

    public function getWithdrawalEmail($username) {
        $query = "SELECT email FROM withdrawal_emails WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        // Fetch and return the email
        return $stmt->fetch(PDO::FETCH_ASSOC)['email'] ?? null;
    }


}
