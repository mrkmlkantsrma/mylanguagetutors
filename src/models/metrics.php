<?php
require_once __DIR__ . '/../config/Database.php';

class AdminMetrics
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    private function dateCondition($timeframe)
    {
        switch ($timeframe) {
            case '30days':
                return "DATE_SUB(CURDATE(), INTERVAL 30 DAY) <= ";

            case 'monthly':
                return "MONTH(";

            case '12months':
                return "DATE_SUB(CURDATE(), INTERVAL 12 MONTH) <= ";

            default:
                return "";
        }
    }

    public function fetchUserMetrics($timeframe = '30days')
    {
        $dateCondition = $this->dateCondition($timeframe);

        $query = "
            SELECT role, COUNT(*) as count
            FROM all_users
            WHERE {$dateCondition} signup_date
            GROUP BY role
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchPaymentMetrics($timeframe = '30days')
    {
        $dateCondition = $this->dateCondition($timeframe);

        $annualAccessFees = "
            SELECT SUM(price) as total
            FROM annual_access_fees
            WHERE {$dateCondition} paid_date
        ";

        $subscriptions = "
            SELECT SUM(price) as total
            FROM subscriptions
            WHERE {$dateCondition} subscribed_at
        ";

        // Adding a query for group class enrollments
        $groupClasses = "
            SELECT SUM(amount_paid) as total
            FROM GroupClassesEnrollments
            WHERE {$dateCondition} date_enrolled
        ";

        $stmt1 = $this->conn->prepare($annualAccessFees);
        $stmt1->execute();
        $annualTotal = $stmt1->fetch(PDO::FETCH_ASSOC);

        $stmt2 = $this->conn->prepare($subscriptions);
        $stmt2->execute();
        $subscriptionsTotal = $stmt2->fetch(PDO::FETCH_ASSOC);

        // Execute the query for group classes and fetch the result
        $stmt3 = $this->conn->prepare($groupClasses);
        $stmt3->execute();
        $groupClassesTotal = $stmt3->fetch(PDO::FETCH_ASSOC);

        return [
            'total' => ($annualTotal['total'] ?? 0) + ($subscriptionsTotal['total'] ?? 0) + ($groupClassesTotal['total'] ?? 0),
            'annual_fee' => $annualTotal['total'] ?? 0,
            'subscription' => $subscriptionsTotal['total'] ?? 0,
            'group_classes' => $groupClassesTotal['total'] ?? 0  // Add this line to include group classes in the return array
        ];
    }

    public function fetchBookingMetrics($timeframe = '30days')
    {
        $dateCondition = $this->dateCondition($timeframe);

        $total = "
            SELECT COUNT(*) as count
            FROM bookings
            WHERE {$dateCondition} date_booked
        ";

        $completed = "
            SELECT COUNT(*) as count
            FROM bookings
            WHERE {$dateCondition} date_booked AND status = 'COMPLETED'
        ";

        $cancelled = "
            SELECT COUNT(*) as count
            FROM bookings
            WHERE {$dateCondition} date_booked AND status = 'CANCELLED'
        ";

        $stmt1 = $this->conn->prepare($total);
        $stmt1->execute();
        $totalBookings = $stmt1->fetch(PDO::FETCH_ASSOC);

        $stmt2 = $this->conn->prepare($completed);
        $stmt2->execute();
        $completedBookings = $stmt2->fetch(PDO::FETCH_ASSOC);

        $stmt3 = $this->conn->prepare($cancelled);
        $stmt3->execute();
        $cancelledBookings = $stmt3->fetch(PDO::FETCH_ASSOC);

        return [
            'total' => $totalBookings['count'] ?? 0,
            'completed' => $completedBookings['count'] ?? 0,
            'cancelled' => $cancelledBookings['count'] ?? 0
        ];
    }
}
?>