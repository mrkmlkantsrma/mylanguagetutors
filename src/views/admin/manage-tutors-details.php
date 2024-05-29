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

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    $adminUsers = new AdminUsers();

    // Fetch the basic user details by username
    $tutorDetails = $adminUsers->getTutorDetailsByEmail($email);

    if (!$tutorDetails) {
        // Handle case when no user found for the given username.
        die('User not found.');
    }

} else {
    // Handle case when no username parameter is provided.
    die('Invalid access.');
}


?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">
    <link rel="shortcut icon" href="assets/images/favicon.png">
    <title>MyLanguageTutor : Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <style>
    .availability-table {
        border-collapse: collapse;
        width: 100%;
    }
    .availability-table th, .availability-table td {
        border: 1px solid #ddd;
        padding: 8px 12px;
        text-align: center;
    }
    .availability-table tr:hover {
        background-color: #f5f5f5;
    }
    .availability-table th {
        background-color: #f2f2f2;
    }
    .available {
        color: green;
    }
    .not-available {
        color: black;
    }
</style>

  </head>
  <body> 

    <div class="site-wrapper">

        <div class="site-header">
            <div class="logo"><img src="./assets/images/logo.png" alt=""></div>
            <div class="site-header-right">
                <div class="site-title">
                    <span class="collapse-nav"><img src="./assets/images/collapse.png" alt=""></span>
                    <h1>Tutor Requests</h1>
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
                    <li><a class="active" href="manage-tutors"><i class="fa-solid fa-list-check"></i> <span>Manage Tutors</span></a></li>
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
                    <h1>Tutor Requests</h1>
                </div>

                <div class="table-area extend mt-0">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">                        
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile">Profile</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#availability">Availability</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#upcomingLesson">Upcoming Lesson</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#lessonHistory">Lesson History</button>
                        </li>
                    </ul>
                    <div class="tab-content pt-3" id="myTabContent">                        
                        <div class="tab-pane fade show active" id="profile">
                            <div class="row">
                                <!-- Displaying Tutor Details -->
                                <?php
                                $details = [
                                    ['label' => 'Username', 'field' => 'username'],
                                    ['label' => 'Country', 'field' => 'country'],
                                    ['label' => 'Full Name', 'field' => function($detail) { return $detail['first_name'] . ' ' . $detail['last_name']; }],
                                    ['label' => 'Speaking Language', 'field' => 'languages_spoken'],
                                    ['label' => 'Mobile No', 'field' => 'mobile_no', 'type' => 'phone'],
                                    ['label' => 'Email Address', 'field' => 'email', 'type' => 'email']
                                ];

                                if ($tutorDetails['role'] !== 'Student') {
                                    $additional_details = [
                                        ['label' => 'Level Taught', 'field' => 'levels_you_teach'],
                                        ['label' => 'Education & Experience', 'field' => 'education_experience'],
                                        ['label' => 'Teaching Languages', 'field' => 'native_language'],
                                        ['label' => 'Working With', 'field' => 'working_with']
                                    ];
                                    $details = array_merge($details, $additional_details);
                                }

                                foreach ($details as $detail) {
                                    $type = isset($detail['type']) ? $detail['type'] : 'text';
                                    $value = is_callable($detail['field']) ? $detail['field']($tutorDetails) : $tutorDetails[$detail['field']];
                                    echo "
                                        <div class='col-sm-6'>
                                            <div class='inp-wrap sm'>
                                                <label>{$detail['label']}</label>
                                                <input class='inp' type='$type' value='" . (empty($value) ? 'Not submitted' : $value) . "' readonly>
                                            </div>
                                        </div>
                                    ";
                                }
                                ?>

                                <!-- Filepaths for Profile Photo, CV, Official ID -->
                                <?php
                                $filepaths = [
                                    ['label' => 'Profile Photo', 'field' => 'profile_photo_filepath']
                                ];

                                if ($tutorDetails['role'] !== 'Student') {
                                    $additional_filepaths = [
                                        ['label' => 'CV Filepath', 'field' => 'cv_filepath'],
                                        ['label' => 'Official ID Filepath', 'field' => 'official_id_filepath']
                                    ];
                                    $filepaths = array_merge($filepaths, $additional_filepaths);
                                }

                                foreach ($filepaths as $filepath) {
                                    $file_value = empty($tutorDetails[$filepath['field']]) ? 'Not submitted' : '../' . $tutorDetails[$filepath['field']];
                                    if ($file_value !== 'Not submitted') {
                                        echo "
                                            <div class='col-sm-6'>
                                                <div class='inp-wrap sm'>
                                                    <label>{$filepath['label']}</label>
                                                    <a href='{$file_value}' target='_blank'> " . basename($file_value) . " - Download</a>
                                                </div>
                                            </div>
                                        ";
                                    } else {
                                        echo "
                                            <div class='col-sm-6'>
                                                <div class='inp-wrap sm'>
                                                    <label>{$filepath['label']}</label>
                                                    <input class='inp' type='text' value='Not submitted' readonly>
                                                </div>
                                            </div>
                                        ";
                                    }
                                }
                                ?>
                            </div>

                            <!-- Tutor Status Update Section -->
                            <div class="searchTop">
                                <form action="<?php echo $_SERVER['PHP_SELF'] . '?email=' . urlencode($email); ?>" method="post">
                                    <div class="searchTop-left">
                                        <p style="white-space: nowrap;"><strong>Edit tutor status</strong></p>
                                        <select class="inp ms-3" name="tutor_status" id="tutor_status">
                                            <option value="">Select Status</option>
                                            <option value="approve">Approve</option>
                                            <option value="unapprove">Request Changes</option>
                                        </select>
                                        <input type="hidden" name="email" value="<?php echo $email; ?>">
                                        <button class="site-link small ms-3" type="submit" name="update_tutor">Update Status</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="availability">
                            <div class="table-responsive">
                                <table class="table theme-table availability-table">
                                    <thead>
                                        <tr>
                                            <th>Day</th>
                                            <th>00:00</th>
                                            <th>01:00</th>
                                            <th>02:00</th>
                                            <th>03:00</th>
                                            <th>04:00</th>
                                            <th>05:00</th>
                                            <th>06:00</th>
                                            <th>07:00</th>
                                            <th>08:00</th>
                                            <th>09:00</th>
                                            <th>10:00</th>
                                            <th>11:00</th>
                                            <th>12:00</th>
                                            <th>13:00</th>
                                            <th>14:00</th>
                                            <th>15:00</th>
                                            <th>16:00</th>
                                            <th>17:00</th>
                                            <th>18:00</th>
                                            <th>19:00</th>
                                            <th>20:00</th>
                                            <th>21:00</th>
                                            <th>22:00</th>
                                            <th>23:00</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Rows will be populated by JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="upcomingLesson">
                            <div class="table-responsive">
                                <table class="table theme-table">
                                    <thead>
                                        <tr>
                                            <th>Date booked</th>
                                            <th>Class Date & Time</th>
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
                                            <td><a href="view-profile.php?username=<?= $lesson['username'] ?>"><?= $lesson['username'] ?></a></td>
                                            <td><?= $lesson['language'] ?></td>
                                            <td><?= $lesson['duration'] ?> mins</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade show" id="lessonHistory">
                            <div class="table-responsive">
                                <table class="table theme-table">
                                    <thead>
                                        <tr>
                                            <th>Date booked</th>
                                            <th>Class Date & Time</th>
                                            <th>Student</th>
                                            <th>Language</th>
                                            <th>Duration</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($tutor_lesson_history)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Not available yet</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($tutor_lesson_history as $history): ?>
                                                <tr>
                                                    <td><?= $history['date_booked'] ?></td>
                                                    <td><?= $history['class_date_time'] ?></td>
                                                    <td><a href="view-profile.php?username=<?= $history['username'] ?>"><?= $history['username'] ?></a></td>
                                                    <td><?= $history['language'] ?></td>
                                                    <td><?= $history['duration'] ?> mins</td>
                                                    <td><?= $history['status'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
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
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    var urlParams = new URLSearchParams(window.location.search);
    var username = urlParams.get('username');
    
    fetch(`/my-language-tutor/src/controllers/adminUsersController.php?action=getTutorAvailability&username=${username}`)
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(text);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            renderAvailability(data.availability);
        } else {
            console.error("Error in data status:", data.message);
        }
    })
    .catch(error => {
        console.error('Fetch error: ', error.message);
    });
});

function renderAvailability(data) {
    const tableBody = document.querySelector(".availability-table tbody");
    
    if (!tableBody) {
        console.error("Couldn't find the table in the DOM.");
        return;
    }
    
    const daysMap = {
        'monday': 'mon',
        'tuesday': 'tue',
        'wednesday': 'wed',
        'thursday': 'thu',
        'friday': 'fri',
        'saturday': 'sat',
        'sunday': 'sun'
    };

    Object.keys(daysMap).forEach(dayKey => {
        const row = document.createElement("tr");
        const dayCell = document.createElement("td");
        dayCell.textContent = capitalizeFirstLetter(dayKey);
        row.appendChild(dayCell);

        const dayData = data[daysMap[dayKey]];
    if(dayData) {
        for (let i = 0; i < 24; i++) {
            const hourCell = document.createElement("td");
            if (dayData.includes(String(i))) {
                hourCell.textContent = 'Available';
                hourCell.classList.add('available'); // Adding class for styles
            } else {
                hourCell.textContent = 'Not Available';
                hourCell.classList.add('not-available'); // Adding class for styles
            }
            row.appendChild(hourCell);
        }
    } else {
        for(let i = 0; i < 24; i++) {
            const hourCell = document.createElement("td");
            hourCell.textContent = "Not Available";
            hourCell.classList.add('not-available');
            row.appendChild(hourCell);
        }
        console.error(`Couldn't find the data for day: ${dayKey}`);
    }

    tableBody.appendChild(row);

    });
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}
</script>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>    
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/custom.js"></script>  
  </body>
</html>
