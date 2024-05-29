<?php
require_once __DIR__ . '/../models/adminUsers.php';

$response = ['success' => false];

if (isset($_POST['ajax']) && $_POST['ajax'] === 'update_withdrawal' && isset($_POST['request_id'])) {
    $adminUsers = new AdminUsers();
    $update = $adminUsers->updateWithdrawalStatusById($_POST['request_id']);

    header('Content-Type: application/json');

    if ($update) {
        $response['success'] = true;
    } else {
        http_response_code(500);
    }
    echo json_encode($response);
}
?>
