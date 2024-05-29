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
    require_once __DIR__ . '/../../models/groupClass.php';

    $groupClass = new GroupClass();
    $tutors = $groupClass->getTutors();

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
    <link rel="stylesheet" href="assets/css/group-classes.css">
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
                    <h1>Create Group Classes</h1>
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
                    <li><a class="active" href="group-classes"><i class="fa-solid fa-user-group"></i><span>Group Classes</span></a></li>
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
                    <h1>Create Group Classes</h1>
                </div>
                    <form action="../../controllers/groupClassController.php" method="POST" enctype="multipart/form-data">
                    <div class="table-area extend">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="title">Title</label>
                                    <input class="inp" type="text" id="title" name="title" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="description">Description</label>
                                    <textarea class="inp" id="description" name="description" rows="3" required 
                                            onkeyup="updateCharacterCount(this)" 
                                            minlength="150" 
                                            maxlength="250"></textarea>
                                    <div id="charCount">0/250</div>
                                    <div id="errorMsg" style="color: red; display: none;">Minimum 150 characters required.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="first-day">First Day</label>
                                    <select class="inp" id="first-day" name="first_day">
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                    </select>
                                    <label for="first-time">Time</label>
                                    <input class="inp" type="time" id="first-time" name="first_time">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="second-day">Second Day</label>
                                    <select class="inp" id="second-day" name="second_day">
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                    </select>
                                    <label for="second-time">Time</label>
                                    <input class="inp" type="time" id="second-time" name="second_time">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="number-of-classes">Number of Classes</label>
                                    <input class="inp" type="number" id="number-of-classes" name="number_of_classes" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="duration">Duration (minutes)</label>
                                    <input class="inp" type="number" id="duration" name="duration" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="tutor">Assign Tutor</label>
                                    <select class="inp" id="tutor" name="tutor" onchange="updateTutorEmail(this)">
                                        <?php foreach ($tutors as $tutor): ?>
                                            <option value="<?php echo $tutor['id']; ?>" data-email="<?php echo $tutor['email']; ?>">
                                                <?php echo $tutor['username']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Hidden input field to hold the tutor's email -->
                            <input type="hidden" id="tutor_email" name="tutor_email" value="">

                            <div class="col-sm-6">
                                <div class="inp-wrap sm">
                                    <label for="pricing">Pricing (USD)</label>
                                    <input class="inp" type="number" step="0.01" id="pricing" name="pricing" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="inp-wrap sm">
                                        <label for="class-cover-image">Cover Image</label>
                                        <input class="inp" type="file" id="class-cover-image" name="class_cover_image" accept="image/*" onchange="previewImage(this)">
                                        <p>Accepted formats: jpg, jpeg, png. Max size: 5MB.</p>
                                        <img id="cover-image-preview" src="" alt="Class Cover Preview" style="max-width: 50%; display: none; margin-top: 10px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="action" value="create">

                        <div class="inp-wrap sm">
                            <input class="site-link sm" type="submit" value="Publish">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="./assets/js/custom.js"></script>
        <script>
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        var preview = document.getElementById('cover-image-preview');
                        preview.src = e.target.result;
                        preview.style.display = 'block'; // display the preview image
                    }

                    reader.readAsDataURL(input.files[0]); // read the data as URL
                }
            }

            function updateCharacterCount(textarea) {
                const charCount = document.getElementById('charCount');
                const errorMsg = document.getElementById('errorMsg');
                const currentLength = textarea.value.length;

                charCount.textContent = `${currentLength}/250`;

                if (currentLength < 150) {
                    textarea.style.color = 'red';
                    errorMsg.style.display = 'block';
                } else {
                    textarea.style.color = 'black';
                    errorMsg.style.display = 'none';
                }
            }

            function updateTutorEmail(selectElement) {
                var email = selectElement.options[selectElement.selectedIndex].getAttribute('data-email');
                document.getElementById('tutor_email').value = email;
            }
        </script>
</body>

</html>