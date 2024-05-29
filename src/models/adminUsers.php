<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/Database.php';

class AdminUsers
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    /**
     * Retrieves a list of users from 'all_users' table along with their latest subscription plan.
     * 
     * @param int $page The current page number.
     * @param int $limit The number of results per page.
     * @return array An array containing the user details and their latest subscription plan.
     */
    public function getAllUsersWithLatestSubscription($page = 1, $limit = 25, $role_filter = '', $subscription_filter = '', $search_term = '') 
    {
        $offset = ($page - 1) * $limit;
    
        $whereClauses = [];
        if ($role_filter) {
            $whereClauses[] = "u.role = :role_filter";
        }
        if ($subscription_filter) {
            $whereClauses[] = "s.plan_name = :subscription_filter";
        }
        if ($search_term) {
            $whereClauses[] = "(u.first_name LIKE :search_term OR u.last_name LIKE :search_term OR u.username LIKE :search_term)";
        }
    
        $whereString = implode(' AND ', $whereClauses);
    
        if ($whereString) {
            $whereString = 'WHERE ' . $whereString;
        }
    
        $sql = "
        SELECT
            u.username,
            u.first_name, 
            u.last_name, 
            u.email, 
            u.role, 
            u.last_login_date,
            s.plan_name
        FROM all_users u
        LEFT JOIN (
            SELECT 
                username, 
                plan_name,
                ROW_NUMBER() OVER(PARTITION BY username ORDER BY id DESC) AS rn
            FROM subscriptions
        ) s ON u.username = s.username AND s.rn = 1
        $whereString
        LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        
        // Bind parameters
        if ($role_filter) {
            $stmt->bindParam(':role_filter', $role_filter);
        }
        if ($subscription_filter) {
            $stmt->bindParam(':subscription_filter', $subscription_filter);
        }
        if ($search_term) {
            $search_term = "%" . $search_term . "%";
            $stmt->bindParam(':search_term', $search_term);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gets the total count of users.
     * 
     * @return int Total number of users.
     */
    public function getTotalUsersCount() 
    {
        $sql = "SELECT COUNT(*) FROM all_users";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchColumn();
    }

    public function getAllTutors($page = 1, $limit = 25, $search_term = '') 
    {
        $offset = ($page - 1) * $limit;

        // Default to selecting Tutors only
        $role_filter = 'Tutor';

        $whereClauses = [];
        $whereClauses[] = "u.role = :role_filter";

        if ($search_term) {
            $whereClauses[] = "(u.first_name LIKE :search_term OR u.last_name LIKE :search_term OR u.username LIKE :search_term)";
        }

        $whereString = implode(' AND ', $whereClauses);

        if ($whereString) {
            $whereString = 'WHERE ' . $whereString;
        }

        $sql = "
        SELECT
            u.username,
            u.first_name, 
            u.last_name, 
            u.email, 
            u.role, 
            u.last_login_date
        FROM all_users u
        $whereString
        LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':role_filter', $role_filter);
        if ($search_term) {
            $search_term = "%" . $search_term . "%";
            $stmt->bindParam(':search_term', $search_term);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalTutorsCount() 
    {
        $sql = "SELECT COUNT(*) FROM all_users WHERE role = 'Tutor'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function getTutorDetailsByEmail($email)
    {
        $sql = "SELECT 
                    username, email, first_name, last_name, mobile_no, 
                    country, education_experience, languages_spoken, native_language,
                    working_with, levels_you_teach, cv_filepath, profile_photo_filepath,
                    official_id_filepath, video_introduction_link, role
                FROM all_users 
                WHERE email = :email";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Retrieves a user's full details by username.
     *
     * @param string $username The username of the user.
     * @return array|false The user's details, or false on failure.
     */
    public function getUserDetailsByUsername($username)
    {
        $sql = "SELECT 
                    username, email, first_name, last_name, mobile_no, 
                    country, education_experience, languages_spoken, native_language,
                    working_with, levels_you_teach, cv_filepath, profile_photo_filepath,
                    official_id_filepath, video_introduction_link, role
                FROM all_users 
                WHERE username = :username";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getActivePlanByUsername($username) {
        $stmt = $this->conn->prepare("SELECT plan_name FROM subscriptions WHERE username = :username ORDER BY id DESC LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCompletedClassesCountByUsername($username) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total_classes FROM bookings WHERE username = :username AND status = 'COMPLETED'");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_classes'];
    }

    public function getLatestAnnualAccessFeeExpireDateByUsername($username) {
        $stmt = $this->conn->prepare("SELECT expire_date FROM annual_access_fees WHERE username = :username ORDER BY id DESC LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAccountStatus($username, $status)
    {
        try {
            $sql = "UPDATE all_users SET disable_account = :status WHERE username = :username";
            $stmt = $this->conn->prepare($sql);
            
            $statusInt = (int)$status;  // Explicitly cast status as an integer
            $stmt->bindParam(':status', $statusInt, PDO::PARAM_INT);
            $stmt->bindParam(':username', $username);

            if (!$stmt->execute()) {
                $error = $stmt->errorInfo();
                throw new Exception("Database error: $error[2]");  // $error[2] has the error message
            }
            
            return true;
        } catch (Exception $e) {
            // You can log this error or display it, depending on your preference
            echo "Error updating account status: " . $e->getMessage();
            return false;
        }
    }

    public function getAccountStatus($username)
    {
        $sql = "SELECT disable_account FROM all_users WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['disable_account'] : null;
    }

    public function getBookings($page = 1, $limit = 25) 
    {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM bookings WHERE status IN ('BOOKED', 'SUBMITTED') LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalBookingsCount()
    {
        $sql = "SELECT COUNT(*) as total FROM bookings WHERE status IN ('BOOKED', 'SUBMITTED')";
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getBookingsHistory($page = 1, $limit = 25) 
    {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM bookings WHERE status IN ('COMPLETED', 'CANCELLED') LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalBookingsHistoryCount()
    {
        $sql = "SELECT COUNT(*) as total FROM bookings WHERE status IN ('COMPLETED', 'CANCELLED')";
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getTutorLessons($tutor_username) 
    {
        $sql = "SELECT * FROM bookings WHERE tutor_username = :tutor_username AND status IN ('BOOKED', 'SUBMITTED')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tutor_username', $tutor_username);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalTutorLessonsCount($tutor_username)
    {
        $sql = "SELECT COUNT(*) as total FROM bookings WHERE tutor_username = :tutor_username AND status IN ('BOOKED', 'SUBMITTED')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tutor_username', $tutor_username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getTutorLessonHistory($tutor_username) 
    {
        $sql = "SELECT * FROM bookings WHERE tutor_username = :tutor_username AND status IN ('COMPLETED', 'CANCELLED')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tutor_username', $tutor_username);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalTutorLessonHistoryCount($tutor_username)
    {
        $sql = "SELECT COUNT(*) as total FROM bookings WHERE tutor_username = :tutor_username AND status IN ('COMPLETED', 'CANCELLED')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tutor_username', $tutor_username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getAvailabilityByUsername($username)
{
    error_log("Model: Getting availability for username: " . $username);
    
    try {
        $query = "SELECT mon, tue, wed, thu, fri, sat, sun FROM availability WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$username]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("Model: Fetched data: " . var_export($result, true)); // Logging the fetched data
        
        if ($result === false) {
            error_log("Model: No result found for username: " . $username);
            throw new Exception("No availability data found for username: " . $username);
        }

        foreach ($result as $day => $hours) {
            if (empty($hours)) {
                $result[$day] = [];
            } else {
                $result[$day] = json_decode($hours, true);
            }
        }

        return $result;
    } catch (PDOException $e) {
        error_log("Model: PDOException - " . $e->getMessage());
        throw $e;
    }
}



    public function getAllPayments($page = 1, $limit = 25) 
    {
        $offset = ($page - 1) * $limit;

        $sql = "
        SELECT 
            'subscription' AS payment_type, 
            order_id, 
            username, 
            subscribed_at AS date 
        FROM subscriptions
        UNION ALL
        SELECT 
            'annual_access_fee' AS payment_type, 
            order_id, 
            username,  
            paid_date AS date 
        FROM annual_access_fees
        ORDER BY date DESC
        LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalPaymentsCount() 
    {
        $sql = "
        SELECT COUNT(*) as total 
        FROM (
            SELECT order_id FROM subscriptions
            UNION ALL
            SELECT order_id FROM annual_access_fees
        ) as combined_payments
        ";

        $stmt = $this->conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getUnapprovedTutors() 
    {
        $sql = "
        SELECT 
            first_name, last_name,  email, username 
        FROM all_users 
        WHERE role = 'Tutor' AND (profile_approved = 0 OR profile_approved IS NULL)
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnapprovedTutorsByChangesRequire() 
    {
        $sql = "
        SELECT 
            first_name, last_name,  email, username 
        FROM all_users 
        WHERE role = 'Tutor' AND (profile_approved = 2)
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnapprovedTutorByEmail($email)
    {
        $sql = "SELECT 
                    username, email, first_name, last_name, mobile_no, 
                    country, education_experience, languages_spoken, native_language,
                    working_with, levels_you_teach, cv_filepath, profile_photo_filepath,
                    official_id_filepath, video_introduction_link, role
                FROM all_users 
                WHERE email = :email";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateTutorAccountStatus($email, $tutor_status)
    {
        $sql = "UPDATE all_users SET profile_approved = :tutor_status WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tutor_status', $tutor_status, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    public function getWithdrawalRequest() 
    {
        $sql = "
        SELECT 
            id, username, requested_amount,  paypal_email, date_time_of_request
        FROM withdrawal_requests 
        WHERE withdrawal_status = 'PENDING';
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getApprovedWithdrawals() 
    {
        $sql = "
        SELECT 
            username, requested_amount, paypal_email, date_time_of_request, withdrawal_status
        FROM withdrawal_requests 
        WHERE withdrawal_status = 'APPROVED'
        ORDER BY date_time_of_request DESC;
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function updateWithdrawalStatusById($requestId, $status = 'APPROVED') {
        $currentDate = date('Y-m-d H:i:s');
        $sql = "
            UPDATE withdrawal_requests
            SET withdrawal_status = :status, response_date = :response_date
            WHERE id = :requestId AND withdrawal_status = 'PENDING';
        ";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':response_date' => $currentDate,
            ':requestId' => $requestId
        ]) && $stmt->rowCount() > 0;
        
    }
    


}

?>
