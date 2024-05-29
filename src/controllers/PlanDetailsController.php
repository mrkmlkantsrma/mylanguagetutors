<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Plan.php';

class PlanDetailsController
{
    private $planModel;

    public function __construct()
    {
        $this->planModel = new Plan();
    }

    public function fetchPlanDetails()
    {
        // Get the username from the session
        $username = $_SESSION['username'];

        // Fetch user's plan details
        $activePlanName = $this->planModel->getActivePlanName($username);
        $numberOfClasses = $this->planModel->getNumberOfClasses($username);

        // Store the details in the session
        $_SESSION['planName'] = $activePlanName ?? 'No active plan';

        if ($numberOfClasses) {
            [$classesUsed, $totalClasses] = explode('/', $numberOfClasses);
            $_SESSION['classesUsed'] = $classesUsed;
            $_SESSION['numberOfClasses'] = $totalClasses;
        } else {
            $_SESSION['classesUsed'] = 0;
            $_SESSION['numberOfClasses'] = 0;
        }

        // Send back the plan details as a JSON response
        echo json_encode([
            'planName' => $activePlanName ?? 'No active plan',
            'classesUsed' => intval($_SESSION['classesUsed']),
            'numberOfClasses' => intval($_SESSION['numberOfClasses']),
        ]);
    }
}

$planDetailsController = new PlanDetailsController();
$planDetailsController->fetchPlanDetails();

?>
