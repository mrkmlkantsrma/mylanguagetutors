<?php

require_once __DIR__ . '/../config/Database.php';

class BillingHistory
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function getBillingHistory($username, $currentPage = 1, $perPage = 15)
    {
        $start = ($currentPage - 1) * $perPage;
        $query = "(
                    SELECT order_id, plan_name, price, subscribed_at, NULL as card_type, NULL as last_4_digits
                    FROM annual_access_fees
                    WHERE username = :username
                )
                UNION ALL
                (
                    SELECT order_id, plan_name, price, subscribed_at, card_type, last_4_digits
                    FROM subscriptions
                    WHERE username = :username
                )
                UNION ALL
                (
                    SELECT '-' as order_id, gc.title as plan_name, gce.amount_paid as price, gce.date_enrolled as subscribed_at, NULL as card_type, NULL as last_4_digits
                    FROM GroupClassesEnrollments gce
                    JOIN GroupClasses gc ON gce.class_id = gc.class_id
                    JOIN all_users au ON gce.student_id = au.id
                    WHERE au.username = :username
                )
                ORDER BY subscribed_at DESC
                LIMIT :start, :perPage";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    public function getTotalRecords($username)
    {
        $query = "SELECT COUNT(*) as total FROM (
                    (
                        SELECT 1
                        FROM annual_access_fees
                        WHERE username = :username1
                    )
                    UNION ALL
                    (
                        SELECT 1
                        FROM subscriptions
                        WHERE username = :username2
                    )
                  ) AS Counts";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':username1', $username);
        $stmt->bindValue(':username2', $username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
