<?php

require_once __DIR__ . '/../config/Database.php';

class Plan
{
    private $conn;
    private $table_name = 'subscriptions';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function getUserLatestSubscription($username)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE username = ? ORDER BY id DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

        public function processSubscription($orderId, $planName, $userPaid, $numberOfClasses, $receipt, $username, $cardType, $last4Digits)
        {
            try {
                $query = "INSERT INTO " . $this->table_name . " (username, order_id, plan_name, price, number_of_classes, receipt, card_type, last_4_digits) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                return $stmt->execute([$username, $orderId, $planName, $userPaid, $numberOfClasses, $receipt, $cardType, $last4Digits]);
            } catch (PDOException $e) {
                error_log($e->getMessage());
                throw $e;
            }
        }

    public function generateReceipt($orderId, $planName, $userPaid, $numberOfClasses)
    {
        // Receipt generation process here. You may want to use a library to generate a PDF,
        // or an HTML template that you fill with the order data and then convert to PDF
        // For the sake of simplicity, let's assume it returns the name of the file where the receipt is stored
        return 'receipts/' . $orderId . '.pdf';
    }

    public function getActivePlanName($username)
    {
        try {
            $query = "SELECT plan_name, number_of_classes, classes_used FROM " . $this->table_name . " WHERE username = ? ORDER BY id DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                // Check if there are remaining classes in the latest subscription
                if ($result['number_of_classes'] > 0 && $result['number_of_classes'] != $result['classes_used']) {
                    return $result['plan_name'];
                } else {
                    return 'No active plan';
                }
            } else {
                return 'No active plan';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getNumberOfClasses($username)
    {
        try {
            $query = "SELECT classes_used, number_of_classes FROM " . $this->table_name . " WHERE username = ? AND number_of_classes > 0 ORDER BY id DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['classes_used'] . '/' . $result['number_of_classes'] : null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    // Cancel Class
  

public function adjustClassesAfterCancellationByUsername($username)
{
    try {
        $subscription = $this->getUserLatestSubscription($username);
        if (!$subscription) {
            return; // No subscription found, so we just return
        }

        // Deduct 1 from classes_used
        $query = "UPDATE " . $this->table_name . " SET classes_used = classes_used - 1 WHERE id = :subscription_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":subscription_id", $subscription['id']);
        $stmt->execute();

        // Check if classes_used went below 0 and adjust accordingly
        $subscription = $this->getUserLatestSubscription($username);
        if ($subscription['classes_used'] < 1) {
            $query = "UPDATE " . $this->table_name . " SET number_of_classes = number_of_classes + 1, classes_used = 0 WHERE id = :subscription_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":subscription_id", $subscription['id']);
            $stmt->execute();
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        throw $e;
    }
}

}
?>
