<?php
require_once __DIR__ . '/../models/adminUsers.php';

$adminUsers = new AdminUsers();
$withdrawalRequests = $adminUsers->getWithdrawalRequest();

foreach ($withdrawalRequests as $request) {
?>
    <tr>
        <td><?= htmlspecialchars($request['date_time_of_request']) ?></td>
        <td><?= htmlspecialchars($request['username']) ?></td>
        <td>$ <?= htmlspecialchars($request['requested_amount']) ?></td>
        <td>                                                    
            <?= htmlspecialchars($request['paypal_email']) ?>
            <i class="fa-regular fa-copy" style="cursor: pointer;" onclick="copyToClipboard('<?= htmlspecialchars($request['paypal_email']) ?>', this)"></i>
        </td>
        <td>
            <a class="site-link small" data-request-id="<?= htmlspecialchars($request['id']) ?>" href="#" onclick="markAsPaid(this);">Mark as Paid</a>
        </td>
    </tr>
<?php
}
?>
