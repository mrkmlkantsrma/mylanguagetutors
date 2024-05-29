<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class WithdrawalRequest {
    private $conn;
    private $apiContext;

    public function __construct() {

        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); 
        $dotenv->load();
    
        $database = new Database();
        $this->conn = $database->dbConnection();
    
        // PayPal Configuration
        $clientId = $_ENV['PAYPAL_WITHDRAWAL_CLIENT_ID'];
        $secret = $_ENV['PAYPAL_WITHDRAWAL_SECRET'];
    
        $this->apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $clientId,
                $secret
            )
        );
    
        // Set PayPal settings
        $this->apiContext->setConfig([
            'mode' => $_ENV['PAYPAL_WITHDRAWAL_MODE'],
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled' => true,
            'log.FileName' => '../logs/PayPal.log',
            'log.LogLevel' => 'ERROR',  // Use 'DEBUG' for development or 'FINE' for production
        ]);
    }
    
    public function createWithdrawalRequest($username, $requestedAmount, $paypalEmail)
    {
        $query = "INSERT INTO withdrawal_requests 
                    (username, requested_amount, paypal_email, date_time_of_request, withdrawal_status, activity, response_date) 
                  VALUES 
                    (:username, :requestedAmount, :paypalEmail, NOW(), 'APPROVED', 'Withdrawal', NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":requestedAmount", $requestedAmount);
        $stmt->bindParam(":paypalEmail", $paypalEmail);

        return $stmt->execute();
    }

    public function deductEarnings($username, $amountToDeduct)
    {
        $query = "UPDATE total_available_earning 
                SET available = available - :amountToDeduct 
                WHERE username = :username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":amountToDeduct", $amountToDeduct, PDO::PARAM_INT);
        $stmt->bindParam(":username", $username);
        
        return $stmt->execute();
    }

    public function sendPayout($paypalEmail, $amount)
    {
        $payouts = new \PayPal\Api\Payout();
        
        $senderBatchHeader = new \PayPal\Api\PayoutSenderBatchHeader();
        $senderBatchHeader->setSenderBatchId(uniqid())
            ->setEmailSubject("You have a payment");

        $recipient = new \PayPal\Api\PayoutItem();
        $recipient->setRecipientType('Email')
            ->setReceiver($paypalEmail)
            ->setSenderItemId(uniqid())
            ->setAmount(new \PayPal\Api\Currency('{
                                "value":"' . $amount . '",
                                "currency":"USD"
                            }'));

        $payouts->setSenderBatchHeader($senderBatchHeader)
            ->addItem($recipient);

        try {
            $output = $payouts->create(array(), $this->apiContext);
            error_log("PayPal API Response: " . json_encode($output));

            return $output;
        } catch (Exception $ex) {
            // Log the error details for debugging
            error_log("PayPal Payout Error: " . $ex->getMessage());
            return false;
        }
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

    public function fetchPendingWithdrawalRequests() {
        $sql = "
            SELECT id, paypal_email, requested_amount, username
            FROM withdrawal_requests
            WHERE withdrawal_status = 'PENDING';
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function processAllPendingWithdrawals() {
        error_log("processAllPendingWithdrawals started");
        
        $pendingRequests = $this->fetchPendingWithdrawalRequests();
        
        error_log("Number of pending requests: " . count($pendingRequests));
        
        foreach ($pendingRequests as $request) {
            $result = $this->processWithdrawal($request['paypal_email'], $request['requested_amount'], $request['id']);
            error_log("Processing result for request ID " . $request['id'] . ": " . ($result ? "Success" : "Failure"));
        }
    }
    
    public function processWithdrawal($paypalEmail, $amount, $requestId) {
        error_log("processWithdrawal started for ID: " . $requestId);
        
        $payoutResponse = $this->sendPayout($paypalEmail, $amount);
    
        // Log the payout response for troubleshooting
        error_log("PayPal Payout Response for ID " . $requestId . ": " . json_encode($payoutResponse));
    
        // Check if the payment was successful
        if ($payoutResponse && $payoutResponse->getBatchHeader()->getBatchStatus() === 'PENDING') {
            // Update the withdrawal status in the database
            $updateResult = $this->updateWithdrawalStatusById($requestId, 'APPROVED');
    
            // Log the result of the database update for troubleshooting
            error_log("Update Withdrawal Status Result for ID " . $requestId . ": " . ($updateResult ? "Success" : "Failure"));
    
            return true;
        } else {
            error_log("Payment was not successful or not pending for ID " . $requestId);
            return false;
        }
    }
    
}

?>