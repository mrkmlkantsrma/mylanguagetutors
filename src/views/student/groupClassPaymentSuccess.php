<?php
// Make sure errors are displayed
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the GroupClassController
require_once '../../controllers/groupClassController.php';

// Instantiate the controller
$groupClassController = new GroupClassController();

// Get the session ID from the query string
$sessionId = $_GET['sessionId'] ?? null;

// Prepare a response array
$response = [];

// Check if we have a session ID
if ($sessionId) {
    // Handle the payment success and enrollment
    $response = $groupClassController->handlePaymentSuccess($sessionId);
} else {
    // If no session ID was provided, return an error
    $response = ['error' => 'No session ID provided'];
}

// Specify that the response is JSON
header('Content-Type: application/json');

// Send the JSON response
echo json_encode($response);
