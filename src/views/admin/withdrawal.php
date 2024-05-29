<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(empty($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    // Store the initially requested page in the session
    $_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];
    
    header('Location: ../../../login.php');
    exit();
}

require_once __DIR__ . '/../../controllers/adminUsersController.php';

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">
    <link rel="shortcut icon" href="assets/images/favicon.png">
    <title>MyLanguageTutor : Withdrawal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</head>

<body>

    <div class="site-wrapper">

        <div class="site-header">
            <div class="logo"><img src="./assets/images/logo.png" alt=""></div>
            <div class="site-header-right">
                <div class="site-title">
                    <span class="collapse-nav"><img src="./assets/images/collapse.png" alt=""></span>
                    <h1>Withdrawal Requests</h1>
                </div>
                <div class="login-head-right">
                    <div class="notific">
                        <!-- <div class="dropdown">
                            <div class="dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fa-regular fa-bell"></i>
                                <span class="notific-count">10</span>
                            </div>
                            <div class="dropdown-menu">
                                <ul>
                                    <li><span><i class="fa-solid fa-volume-low"></i></span> Lorem Ipsum is simply dummy text of the printing and typesetting industry.</li>
                                    <li><span><i class="fa-solid fa-volume-low"></i></span> Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</li>
                                    <li><span><i class="fa-solid fa-volume-low"></i></span> It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. </li>
                                </ul>
                            </div>
                        </div> -->
                    </div>
                    <div class="profile-dropdown">
                    <div class="dropdown">
                            <div class="dropdown-toggle" data-bs-toggle="dropdown">
                                <span class="profile-dropdown-img"><img src="https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&amp;cs=tinysrgb&amp;w=1260&amp;h=750&amp;dpr=1" alt=""></span>
                                <span class="btn-txt">Admin</span>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-wrap">
            <div class="side-nav">
                <ul>
                    <li><a href="overview"><i class="fa-solid fa-chart-column"></i> <span>Overview</span></a></li>
                    <li><a href="users"><i class="fa-solid fa-users"></i> <span>Users</span></a></li>
                    <li><a href="lessons"><i class="fa-solid fa-book"></i> <span>Lessons</span></a></li>
                    <li><a href="group-classes"><i class="fa-solid fa-user-group"></i><span>Group Classes</span></a></li>
                    <li><a class="active" href="withdrawal"><i class="fa-solid fa-receipt"></i> <span>Withdrawal</span></a></li>
                    <?php
                    $numberOfUnapprovedTutors = count($unapprovedTutors);
                    ?>

                    <li>
                        <a href="tutor-request">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Tutor Request</span>
                            <span class="notification-count">+<?= $numberOfUnapprovedTutors ?></span>
                        </a>
                    </li>
                    <li><a href="manage-tutors"><i class="fa-solid fa-list-check"></i> <span>Manage Tutors</span></a></li>
                    <li><a href="payments"><i class="fa-regular fa-money-bill-1"></i> <span>Payments</span></a></li>
                    <li><a href="settings"><i class="fa-solid fa-gear"></i> <span>Settings</span></a></li>
                    <li>
                        <form action="../../controllers/LoginController.php" method="post" style="display: inline;">
                            <input type="hidden" name="logout" value="1">
                            <a href="#" onclick="this.closest('form').submit(); return false;">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                <span>Logout</span>
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="main-container">

                <div class="page-title-mob">
                    <h1>Withdrawal Requests</h1>
                </div>

                <div class="table-area extend mt-0">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">                        
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-1">Completed</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-2">Withdrawal Requests</button>
                        </li>
                    </ul>
                    <div class="tab-content pt-3" id="myTabContent">                        
                        <div class="tab-pane fade show active" id="tab-1">
                            <div class="table-responsive">
                                <table class="table theme-table">
                                    <thead>
                                        <th>DATE & TIME</th>
                                        <th>USERNAME <span><img src="./assets/images/filter.png" alt=""></span></th>
                                        <th>AMOUNT <span><img src="./assets/images/filter.png" alt=""></span></th>
                                        <th>PAYPAL EMAIL <span><img src="./assets/images/filter.png" alt=""></span></th>
                                        <th>STATUS <span><img src="./assets/images/filter.png" alt=""></span></th>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach ($ApprovedWithdrawals as $approve) {
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($approve['date_time_of_request']) ?></td>
                                            <td><?= htmlspecialchars($approve['username']) ?></td>
                                            <td>$ <?= htmlspecialchars($approve['requested_amount']) ?></td>
                                            <td><?= htmlspecialchars($approve['paypal_email']) ?></td>
                                            <td><?= htmlspecialchars($approve['withdrawal_status']) ?></td>
                                        </tr>
                                        <?php
                                            }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="tab-2">
                            <div class="table-responsive">
                                <table class="table theme-table" id="withdrawalTable">
                                    <thead>
                                        <th>DATE & TIME</th>
                                        <th>USERNAME <span><img src="./assets/images/filter.png" alt=""></span></th>
                                        <th>AMOUNT <span><img src="./assets/images/filter.png" alt=""></span></th>
                                        <th>PAYPAL EMAIL <span><img src="./assets/images/filter.png" alt=""></span></th>
                                        <th>ACTION <span><img src="./assets/images/filter.png" alt=""></span></th>
                                    </thead>
                                    <tbody>
                                        <?php
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
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="./assets/js/custom.js"></script>

        <script>
            function copyToClipboard(text, element) {
                // Copy to clipboard logic
                var textArea = document.createElement("textarea");
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);

                // Display 'Copied!' text
                var copiedText = document.createElement("span");
                copiedText.innerHTML = " Copied!";
                element.parentNode.insertBefore(copiedText, element.nextSibling);

                // Remove 'Copied!' text after 2 seconds
                setTimeout(function() {
                    element.parentNode.removeChild(copiedText);
                }, 2000);
            }
        </script>

        <script>
            function markAsPaid(link) {
                const requestId = link.dataset.requestId;
                link.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Processing...';

                fetch('../../controllers/update_withdrawal_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `ajax=update_withdrawal&request_id=${requestId}`
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            refreshTable();
                        } else {
                            alert("There was an error processing the request.");
                            link.textContent = "Mark as Paid";
                        }
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:', error.message);
                        link.textContent = "Mark as Paid";
                    });
            }

            function refreshTable() {
                fetch('../../controllers/fetch_withdrawal_requests.php')
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('withdrawalTable').tBodies[0].innerHTML = data;
                    });
            }
        </script>

        <script>
            $(document).ready(function() {
                $(".nav-link[data-bs-target='#tab-1']").click(function() {
                    $.ajax({
                        url: '../../controllers/fetch_approved_withdrawals.php',
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            let rows = '';
                            data.forEach(function(approve) {
                                rows += `
                        <tr>
                            <td>${htmlspecialchars(approve.date_time_of_request)}</td>
                            <td>${htmlspecialchars(approve.username)}</td>
                            <td>$ ${htmlspecialchars(approve.requested_amount)}</td>
                            <td>${htmlspecialchars(approve.paypal_email)}</td>
                            <td>${htmlspecialchars(approve.withdrawal_status)}</td>
                        </tr>
                    `;
                            });

                            $("#tab-1 table tbody").html(rows);
                        },
                        error: function(error) {
                            console.error("There was an error fetching the data:", error);
                        }
                    });
                });
            });

            function htmlspecialchars(str) {
                // A basic JavaScript version of PHP's htmlspecialchars
                if (typeof(str) == "string") {
                    str = str.replace(/&/g, "&amp;"); /* must do &amp; first */
                    str = str.replace(/"/g, "&quot;");
                    str = str.replace(/'/g, "&#039;");
                    str = str.replace(/</g, "&lt;");
                    str = str.replace(/>/g, "&gt;");
                }
                return str;
            }
        </script>


</body>

</html>