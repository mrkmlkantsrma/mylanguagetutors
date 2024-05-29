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
    <title>MyLanguageTutor : Lessons</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
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
                    <h1>Lessons</h1>
                </div>
                <div class="login-head-right">
                    <!-- <div class="notific">
                        <div class="dropdown">
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
                        </div>
                    </div> -->
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
                    <li><a class="active" href="lessons"><i class="fa-solid fa-book"></i> <span>Lessons</span></a></li>
                    <li><a href="group-classes"><i class="fa-solid fa-user-group"></i><span>Group Classes</span></a></li>
                    <li><a href="withdrawal"><i class="fa-solid fa-receipt"></i> <span>Withdrawal</span></a></li>
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
                    <h1>Lessons</h1>
                </div>

                <div class="table-area extend mt-0">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-1">Upcoming</button>
                        </li>
                        <li class="nav-item">
                          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-2">History</button>
                        </li>
                      </ul>
                      <div class="tab-content pt-3" id="myTabContent">
                        <div class="tab-pane fade show active" id="tab-1">
                            <div class="table-responsive">
                                <table class="table theme-table">
                                    <thead>
                                        <tr>
                                            <th>Date booked</th>
                                            <th>Class Date & Time</th>
                                            <th>Tutor</th>
                                            <th>Student</th>
                                            <th>Language</th>
                                            <th>Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tutor_lessons as $lesson): ?>
                                        <tr>
                                            <td><?= $lesson['date_booked'] ?></td>
                                            <td><?= $lesson['class_date_time'] ?></td>
                                            <td><a href="view-profile.php?username=<?= $lesson['tutor_username'] ?>"><?= $lesson['tutor_username'] ?></a></td>
                                            <td><a href="view-profile.php?username=<?= $lesson['username'] ?>"><?= $lesson['username'] ?></a></td>
                                            <td><?= $lesson['language'] ?></td>
                                            <td><?= $lesson['duration'] ?> mins</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="6">
                                            <span>Showing <?= (($tutor_page - 1) * $tutor_limit) + 1 ?>-<?= min($tutor_page * $tutor_limit, $totalTutorLessons) ?> of Total <?= $totalTutorLessons ?></span>
                                            <span class="table-nav">
                                                <a href="?page=<?= max(1, $tutor_page - 1) ?>"><i class="fa-solid fa-arrow-left"></i></a>
                                                <span><?= $tutor_page ?></span>
                                                <a href="?page=<?= min(ceil($totalTutorLessons / $tutor_limit), $tutor_page + 1) ?>"><i class="fa-solid fa-arrow-right"></i></a>
                                            </span>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                        <div class="tab-pane fade" id="tab-2">
                            <div class="table-responsive">
                                <table class="table theme-table">
                                    <thead>
                                        <tr>
                                            <th>Date booked <span><img src="./assets/images/filter.png" alt=""></span></th>
                                            <th>Class Date & Time <span><img src="./assets/images/filter.png" alt=""></span></th>
                                            <th>Tutor <span><img src="./assets/images/filter.png" alt=""></span></th>
                                            <th>Student <span><img src="./assets/images/filter.png" alt=""></span></th>
                                            <th>Language <span><img src="./assets/images/filter.png" alt=""></span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <?php foreach ($bookingsHistory as $bookingHistory): ?>
                                            <tr>
                                                <td><?= $bookingHistory['date_booked'] ?></td>
                                                <td><?= $bookingHistory['class_date_time'] ?></td>
                                                <td><a href="view-profile.php?username=<?= $bookingHistory['tutor_username'] ?>"><?= $bookingHistory['tutor_username'] ?></a></td>
                                                <td><a href="view-profile.php?username=<?= $bookingHistory['username'] ?>"><?= $bookingHistory['username'] ?></a></td>
                                                <td><?= $bookingHistory['language'] ?></td>
                                                <td><?= $bookingHistory['duration'] ?> mins</td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="6">
                                                <span>Showing <?= (($page - 1) * $limit) + 1 ?>-<?= min($page * $limit, $totalBookingsHistory) ?> of Total <?= $totalBookingsHistory ?></span>
                                                <span class="table-nav">
                                                    <a href="?page=<?= max(1, $page - 1) ?>"><i class="fa-solid fa-arrow-left"></i></a>
                                                    <span><?= $page ?></span>
                                                    <a href="?page=<?= min(ceil($totalBookingsHistory / $limit), $page + 1) ?>"><i class="fa-solid fa-arrow-right"></i></a>
                                                </span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                      </div>
                </div>

            </div>
        </div>

  </div>  

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>    
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/custom.js"></script>  
  </body>
</html>
