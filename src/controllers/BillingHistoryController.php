<?php

// Require the BillingHistory model
require_once __DIR__ . '/../models/BillingHistory.php';

/**
 * BillingHistoryController Class
 * 
 * Manages the billing history operations
 */
class BillingHistoryController {
    // Variable to hold the BillingHistory model
    private $billingHistoryModel;

    /**
     * Constructor
     * 
     * Initializes the BillingHistory model
     */
    public function __construct() {
        $this->billingHistoryModel = new BillingHistory();
    }

    /**
     * Get Billing History
     * 
     * Fetches the billing history of a specific user.
     * 
     * @param string $username     The username of the user
     * @param int $currentPage     The current page for pagination
     * @param int $perPage         The number of records per page
     * 
     * @return array $history      The billing history
     */
    public function getBillingHistory($username, $currentPage = 1, $perPage = 15) {
        $history = $this->billingHistoryModel->getBillingHistory($username, $currentPage, $perPage);
        return $history;
    }

    /**
     * Get Total Records
     * 
     * Fetches the total number of billing records for a specific user.
     * 
     * @param string $username     The username of the user
     * 
     * @return int $totalRecords   The total number of billing records
     */
    public function getTotalRecords($username) {
        $totalRecords = $this->billingHistoryModel->getTotalRecords($username);
        return $totalRecords;
    }
}
