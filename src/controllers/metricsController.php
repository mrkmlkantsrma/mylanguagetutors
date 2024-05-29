<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/metrics.php';

class MetricsController {

    private $metricsModel;

    public function __construct() {
        $this->metricsModel = new AdminMetrics();
    }

    public function get30DaysMetrics() {
        $userMetrics = $this->metricsModel->fetchUserMetrics();
        $paymentMetrics = $this->metricsModel->fetchPaymentMetrics();
        $bookingMetrics = $this->metricsModel->fetchBookingMetrics();

        return [
            'users' => $this->organizeUserMetrics($userMetrics),
            'payments' => $paymentMetrics,
            'bookings' => $bookingMetrics
        ];
    }

    private function organizeUserMetrics($userMetrics) {
        $organizedMetrics = [
            'total' => 0,
            'students' => 0,
            'tutors' => 0
        ];

        foreach ($userMetrics as $metric) {
            if ($metric['role'] == 'Student') {
                $organizedMetrics['students'] = (int) $metric['count'];
            } else if ($metric['role'] == 'Tutor') {
                $organizedMetrics['tutors'] = (int) $metric['count'];
            }
            $organizedMetrics['total'] += (int) $metric['count'];
        }

        return $organizedMetrics;
    }
}

// Usage
$controller = new MetricsController();
$thirtyDaysMetrics = $controller->get30DaysMetrics();
