<?php

require_once __DIR__ . '/../models/Zoom.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'createMeeting') {
        $username = $_POST['username'];
        $dateTime = $_POST['dateTime'];
        $timezone = $_POST['timezone']; 

        $zoom = new Zoom();

        try {
            // Create the meeting using the Zoom class
            $meetingURL = $zoom->createMeeting($username, $dateTime, $timezone);

            // Send a success status and the Zoom meeting link back to the client
            echo json_encode(['status' => 'success', 'meetingURL' => $meetingURL]);
        } catch (Exception $e) {
            // If an exception was thrown, log it and send an error status back to the client
            error_log($e->getMessage());
            echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
        }
    }
}

?>
