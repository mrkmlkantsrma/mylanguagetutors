<?php
    require_once __DIR__ . '/../models/adminUsers.php';

    $adminUsers = new AdminUsers();  // Instantiate your AdminUsers class
    $ApprovedWithdrawals = $adminUsers->getApprovedWithdrawals();
    echo json_encode($ApprovedWithdrawals);
?>