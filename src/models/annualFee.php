<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/Database.php';

class AnnualFee
{
    private $conn;
    private $table_name = "annual_access_fees";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function getAnnualFee()
    {
        return 30;
    }

    public function getUserPaymentStatus($username)
    {
        $sql = "SELECT username, paid_date, expire_date, classes, annual_fee_paid FROM " . $this->table_name . " WHERE username = ? AND NOW() < expire_date ORDER BY expire_date DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        } else {
            return null;
        }
    }

    public function processAnnualFee($username, $orderId)
    {
        $this->conn->beginTransaction();

        try {
            $sql =
                "INSERT INTO " . $this->table_name . " (username, order_id, plan_name, price, classes, annual_fee_paid, paid_date, expire_date) VALUES (?, ?, 'Annual Access Fee', 30, 1, 1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR))";
            error_log('Executing SQL: ' . $sql);
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$username, $orderId]);

            $this->conn->commit();

            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("processAnnualFee exception: " . $e->getMessage() . " (code " . $e->getCode() . ")");
            return false;
        }
    }
}
