<?php

if(session_status() == PHP_SESSION_NONE) {

    session_start();

}



if(empty($_SESSION['username']) || $_SESSION['role'] !== 'Tutor') {

    // Store the initially requested page in the session

    $_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];

    

    header('Location: ../../../login.php');

    exit();

}



require_once __DIR__ . '/../../controllers/UserController.php';

require_once __DIR__ . '/../../models/User.php';



$user = new User();



$username = $_SESSION['username'];

$userData = $user->getUserData($username);

$_SESSION['user_data'] = $userData;

// check if user data is set in session

if (isset($_SESSION['user_data'])) {

    $userData = $_SESSION['user_data'];

    $username = $userData['username'];

    $firstName = $userData['first_name'];

    $lastName = $userData['last_name'];

    $email = $userData['email'];

    $mobileNo = $userData['mobile_no'];

    $country = $userData['country'];

    $languagesSpoken = $userData['languages_spoken'];

    $languageAndEducationLevel = $userData['language_and_education_level'];

    $profilePicture = $userData['profile_photo_filepath'];

    $educationExperience = $userData['education_experience'];

    $nativeLanguage = $userData['native_language'];

    $workingWith = isset($userData['working_with']) && $userData['working_with'] !== null

    ? explode(',', $userData['working_with'])

    : [];



$levelsYouTeach = isset($userData['levels_you_teach']) && $userData['levels_you_teach'] !== null

    ? explode(',', $userData['levels_you_teach'])

    : [];



    $cvTarget = $userData['official_id_filepath'];

}



$is_disabled = "";  // Set a default value for the variable



if(isset($_SESSION['username'])) {

    $username = $_SESSION['username'];

    $profile_status = $user->profileApproved($username);



    if ($profile_status === 1) {

        $status_inner = "";

        $status_text = "Status: Approved";

        $is_disabled = "disabled";

        $is_required = "";

        $required_mark = "";

    } elseif ($profile_status === 0) {

        $status_text = "Status: pending";

        $is_disabled = "disabled";

        $is_required = "";

        $required_mark = "";

    } elseif ($profile_status === 2) {

        $status_text = "Status: Unapproved";

        $is_required = "required";

        $required_mark = "<span class='required'>*</span>";

    } elseif ($profile_status === null) {

        $status_text = "Not Submitted";

        $is_required = "required";

        $required_mark = "<span class='required'>*</span>";

    }

} else {

    // Handle scenario where session variable 'username' is not set. 

    // You could redirect to login page or show a message.

}

?>

<!DOCTYPE html>

<html lang="en">



<head>

    <meta charset="utf-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no" />

    <link rel="shortcut icon" href="assets/images/favicon.png" />

    <title>MyLanguageTutor : Profile</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="assets/css/custom.css" />

    <script type="text/javascript">

    function googleTranslateElementInit() {

      new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');

    }

    </script>

    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <style>

        /* Add necessary styling for the upload field */

        .upload-field {

          border: 2px solid #ccc;

          padding: 10px;

          text-align: center;

          cursor: pointer;

          position: relative;

        }

        

        /* Style the document icon */

        .upload-field i.fa-file {

          font-size: 24px;

          margin-bottom: 10px;

        }

        

        /* Style the file name text */

        .upload-field p {

          font-size: 14px;

          margin: 0;

          white-space: nowrap;

          overflow: hidden;

          text-overflow: ellipsis;

        }

        

        /* Hide the default file input appearance */

        .upload-field input[type="file"] {

          position: absolute;

          top: 0;

          left: 0;

          width: 100%;

          height: 100%;

          opacity: 0;

          cursor: pointer;

        }

        

        .required {

            color: red;

        }

        

    </style>

</head>



<body>

    <div class="site-wrapper">

        <div class="site-header">

            <div class="logo"><img src="./assets/images/logo.png" alt="" /></div>

            <div class="site-header-right">

                <div class="site-title">

                    <span class="collapse-nav"><img src="./assets/images/collapse.png" alt="" /></span>

                    <h1>Profile</h1>

                </div>

                <div class="login-head-right">

                    

                    <div class="profile-dropdown">

                        <div class="dropdown">

                            <div class="dropdown-toggle" data-bs-toggle="dropdown">

                                <span class="profile-dropdown-img">

                                    <?php

                                    if(isset($profilePicture) && !empty($profilePicture)) {

                                        $substring_to_check = "https://lh3.googleusercontent.com";

                                        if (substr($profilePicture, 0, strlen($substring_to_check)) === $substring_to_check) {
                                            $profilePictureUrl = $profilePicture;
                                        } else {
                                            $profilePicture = str_replace('../../', '', $profilePicture);

                                            $profilePictureUrl = '/' . $profilePicture;
                                        }

                                        echo "<img src='" . $profilePictureUrl . "' alt='Profile Picture' />";

                                    } else {

                                        echo "<img src='https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg' />";

                                    }

                                ?>

                                </span>

                                <span class="btn-txt"> <?php echo $username = $userData['username']; ?></span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <div class="dashboard-wrap">

            <div class="side-nav">

                <ul>

                    <li>

                        <a href="my-lessons"><i class="fa-regular fa-newspaper"></i> <span>My lessons</span></a>

                    </li>

                    <li><a class="active" href="group-classes"><i class="fa-solid fa-user-group"></i><span>Group Classes</span></a></li>

                    <li>

                        <a href="earnings-payment"><i class="fa-regular fa-credit-card"></i> <span>Earnings & payments</span></a>

                    </li>

                    <li>

                        <a class="active" href="profile"><i class="fa-solid fa-user-astronaut"></i> <span>My profile</span></a>

                    </li>

                    <li>

                        <a href="reviews"><i class="fa-solid fa-certificate"></i> <span>Reviews</span></a>

                    </li>

                    <li>

                        <a href="help-support"><i class="fa-solid fa-circle-info"></i> <span>Help & support</span></a>

                    </li>

                    <li>

                        <form action="../../controllers/LoginController.php" method="post" style="display: inline;">

                            <input type="hidden" name="logout" value="1" />

                            <a href="#" onclick="this.closest('form').submit(); return false;">

                                <i class="fa-solid fa-arrow-right-to-bracket"></i>

                                <span>Logout</span>

                            </a>

                        </form>

                    </li>

                </ul>

            </div>

            <div class="main-container">

            <?php 

                if(isset($_SESSION['message'])) {

                    echo '<div class="alert alert-success">'.$_SESSION['message'].'</div>';

                    unset($_SESSION['message']); // remove it after displaying

                }

                if(isset($_SESSION['error'])) {

                    echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';

                    unset($_SESSION['error']); // remove it after displaying

                }

            ?>



                <div class="page-title-mob">

                    <h1>Profile</h1>

                </div>

                <div class="table-area extend">

                    <div class="profile-top">

                        <div class="profile-pic">

                            <?php

                                    if(isset($profilePicture) && !empty($profilePicture)) {
                                        
                                        $substring_to_check = "https://lh3.googleusercontent.com";

                                        if (substr($profilePicture, 0, strlen($substring_to_check)) === $substring_to_check) {
                                            $profilePictureUrl = $profilePicture;
                                        } else {
                                            $profilePicture = str_replace('../../', '', $profilePicture);

                                            $profilePictureUrl = '/' . $profilePicture;
                                        }

                                        echo "<img src='" . $profilePictureUrl . "' alt='Profile Picture' />";

                                    } else {

                                        echo "<img src='https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg' />";

                                    }

                                ?>

                            <!-- <div class="edit-pic">

                                    <i class="fa-regular fa-pen-to-square"></i>

                                    <input type="file" />

                                </div> -->

                        </div>

                        <div class="profile-description">

                            <h2 class="title">Bio</h2>

                            <p>

                                <?php echo $educationExperience = $userData['education_experience']; ?>

                            </p>

                        </div>

                    </div>

                </div>



                <form action="../../controllers/UserController.php" method="post" enctype="multipart/form-data">

                    <div class="table-area extend">

                        <div class="tab-content tab-content-border">

                            <div class="tab-pane fade show active" id="tab-1">

                                <div class="row">

                                    <div class="col-sm-6">

                                        <div class="inp-wrap sm">

                                            <label for="username">Username <?php echo $required_mark; ?></label>

                                            <input id="username" name="username" class="inp" type="text" value="<?php echo $username = $userData['username']; ?>" readonly />

                                        </div>

                                    </div>

                                    <div class="col-sm-6">

                                        <div class="inp-wrap sm">

                                            <label for="email">Email Address <?php echo $required_mark; ?></label>

                                            <input id="email" name="email" class="inp" type="email" value="<?php echo $email = $userData['email']; ?>" readonly />

                                        </div>

                                    </div>

                                    <div class="col-sm-6">

                                        <div class="inp-wrap sm">

                                            <label for="firstName">First Name <?php echo $required_mark; ?></label>

                                            <input id="firstName" name="firstName" class="inp" type="text" value="<?php echo $firstName = $userData['first_name']; ?>" <?php echo $is_required; ?>/>

                                        </div>

                                    </div>

                                    <div class="col-sm-6">

                                        <div class="inp-wrap sm">

                                            <label for="lastName">Last Name <?php echo $required_mark; ?></label>

                                            <input id="lastName" name="lastName" class="inp" type="text" value="<?php echo $lastName = $userData['last_name']; ?>" <?php echo $is_required; ?>/>

                                        </div>

                                    </div>

                                    <div class="col-sm-6">

                                        <div class="inp-wrap sm">

                                            <label for="phone">Mobile No</label>

                                            <input id="phone" name="mobileNo" class="inp" type="tel" value="<?php echo $mobileNo = $userData['mobile_no']; ?>" />

                                        </div>

                                    </div>

                                    <div class="col-sm-6">

                                        <div class="inp-wrap sm">

                                            <label for="country">country <?php echo $required_mark; ?></label>

                                            <select class="inp" id="country" name="country" <?php echo $is_required; ?>>

                                                <option value="" disabled>Select a country</option>

                                                <option value="Afghanistan" <?php if($userData['country'] == "Afghanistan") echo 'selected'; ?>>Afghanistan</option>

                                                <option value="Albania" <?php if($userData['country'] == "Albania") echo 'selected'; ?>>Albania</option>

                                                <option value="Algeria" <?php if($userData['country'] == "Algeria") echo 'selected'; ?>>Algeria</option>

                                                <option value="Andorra" <?php if($userData['country'] == "Andorra") echo 'selected'; ?>>Andorra</option>

                                                <option value="Angola" <?php if($userData['country'] == "Angola") echo 'selected'; ?>>Angola</option>

                                                <option value="Antigua and Barbuda" <?php if($userData['country'] == "Antigua and Barbuda") echo 'selected'; ?>>Antigua and Barbuda</option>

                                                <option value="Argentina" <?php if($userData['country'] == "Argentina") echo 'selected'; ?>>Argentina</option>

                                                <option value="Armenia" <?php if($userData['country'] == "Armenia") echo 'selected'; ?>>Armenia</option>

                                                <option value="Australia" <?php if($userData['country'] == "Australia") echo 'selected'; ?>>Australia</option>

                                                <option value="Austria" <?php if($userData['country'] == "Austria") echo 'selected'; ?>>Austria</option>

                                                <option value="Azerbaijan" <?php if($userData['country'] == "Azerbaijan") echo 'selected'; ?>>Azerbaijan</option>

                                                <option value="Bahamas" <?php if($userData['country'] == "Bahamas") echo 'selected'; ?>>Bahamas</option>

                                                <option value="Bahrain" <?php if($userData['country'] == "Bahrain") echo 'selected'; ?>>Bahrain</option>

                                                <option value="Bangladesh" <?php if($userData['country'] == "Bangladesh") echo 'selected'; ?>>Bangladesh</option>

                                                <option value="Barbados" <?php if($userData['country'] == "Barbados") echo 'selected'; ?>>Barbados</option>

                                                <option value="Belarus" <?php if($userData['country'] == "Belarus") echo 'selected'; ?>>Belarus</option>

                                                <option value="Belgium" <?php if($userData['country'] == "Belgium") echo 'selected'; ?>>Belgium</option>

                                                <option value="Belize" <?php if($userData['country'] == "Belize") echo 'selected'; ?>>Belize</option>

                                                <option value="Benin" <?php if($userData['country'] == "Benin") echo 'selected'; ?>>Benin</option>

                                                <option value="Bhutan" <?php if($userData['country'] == "Bhutan") echo 'selected'; ?>>Bhutan</option>

                                                <option value="Bolivia" <?php if($userData['country'] == "Bolivia") echo 'selected'; ?>>Bolivia</option>

                                                <option value="Bosnia and Herzegovina" <?php if($userData['country'] == "Bosnia and Herzegovina") echo 'selected'; ?>>Bosnia and Herzegovina</option>

                                                <option value="Botswana" <?php if($userData['country'] == "Botswana") echo 'selected'; ?>>Botswana</option>

                                                <option value="Brazil" <?php if($userData['country'] == "Brazil") echo 'selected'; ?>>Brazil</option>

                                                <option value="Brunei" <?php if($userData['country'] == "Brunei") echo 'selected'; ?>>Brunei</option>

                                                <option value="Bulgaria" <?php if($userData['country'] == "Bulgaria") echo 'selected'; ?>>Bulgaria</option>

                                                <option value="Burkina Faso" <?php if($userData['country'] == "Burkina Faso") echo 'selected'; ?>>Burkina Faso</option>

                                                <option value="Burundi" <?php if($userData['country'] == "Burundi") echo 'selected'; ?>>Burundi</option>

                                                <option value="Cabo Verde" <?php if($userData['country'] == "Cabo Verde") echo 'selected'; ?>>Cabo Verde</option>

                                                <option value="Cambodia" <?php if($userData['country'] == "Cambodia") echo 'selected'; ?>>Cambodia</option>

                                                <option value="Cameroon" <?php if($userData['country'] == "Cameroon") echo 'selected'; ?>>Cameroon</option>

                                                <option value="Canada" <?php if($userData['country'] == "Canada") echo 'selected'; ?>>Canada</option>

                                                <option value="Central African Republic" <?php if($userData['country'] == "Central African Republic") echo 'selected'; ?>>Central African Republic</option>

                                                <option value="Chad" <?php if($userData['country'] == "Chad") echo 'selected'; ?>>Chad</option>

                                                <option value="Chile" <?php if($userData['country'] == "Chile") echo 'selected'; ?>>Chile</option>

                                                <option value="China" <?php if($userData['country'] == "China") echo 'selected'; ?>>China</option>

                                                <option value="Colombia" <?php if($userData['country'] == "Colombia") echo 'selected'; ?>>Colombia</option>

                                                <option value="Comoros" <?php if($userData['country'] == "Comoros") echo 'selected'; ?>>Comoros</option>

                                                <option value="Congo" <?php if($userData['country'] == "Congo") echo 'selected'; ?>>Congo</option>

                                                <option value="Costa Rica" <?php if($userData['country'] == "Costa Rica") echo 'selected'; ?>>Costa Rica</option>

                                                <option value="Croatia" <?php if($userData['country'] == "Croatia") echo 'selected'; ?>>Croatia</option>

                                                <option value="Cuba" <?php if($userData['country'] == "Cuba") echo 'selected'; ?>>Cuba</option>

                                                <option value="Cyprus" <?php if($userData['country'] == "Cyprus") echo 'selected'; ?>>Cyprus</option>

                                                <option value="Czechia" <?php if($userData['country'] == "Czechia") echo 'selected'; ?>>Czechia</option>

                                                <option value="Denmark" <?php if($userData['country'] == "Denmark") echo 'selected'; ?>>Denmark</option>

                                                <option value="Djibouti" <?php if($userData['country'] == "Djibouti") echo 'selected'; ?>>Djibouti</option>

                                                <option value="Dominica" <?php if($userData['country'] == "Dominica") echo 'selected'; ?>>Dominica</option>

                                                <option value="Dominican Republic" <?php if($userData['country'] == "Dominican Republic") echo 'selected'; ?>>Dominican Republic</option>

                                                <option value="Ecuador" <?php if($userData['country'] == "Ecuador") echo 'selected'; ?>>Ecuador</option>

                                                <option value="Egypt" <?php if($userData['country'] == "Egypt") echo 'selected'; ?>>Egypt</option>

                                                <option value="El Salvador" <?php if($userData['country'] == "El Salvador") echo 'selected'; ?>>El Salvador</option>

                                                <option value="Equatorial Guinea" <?php if($userData['country'] == "Equatorial Guinea") echo 'selected'; ?>>Equatorial Guinea</option>

                                                <option value="Eritrea" <?php if($userData['country'] == "Eritrea") echo 'selected'; ?>>Eritrea</option>

                                                <option value="Estonia" <?php if($userData['country'] == "Estonia") echo 'selected'; ?>>Estonia</option>

                                                <option value="Eswatini" <?php if($userData['country'] == "Eswatini") echo 'selected'; ?>>Eswatini</option>

                                                <option value="Ethiopia" <?php if($userData['country'] == "Ethiopia") echo 'selected'; ?>>Ethiopia</option>

                                                <option value="Fiji" <?php if($userData['country'] == "Fiji") echo 'selected'; ?>>Fiji</option>

                                                <option value="Finland" <?php if($userData['country'] == "Finland") echo 'selected'; ?>>Finland</option>

                                                <option value="France" <?php if($userData['country'] == "France") echo 'selected'; ?>>France</option>

                                                <option value="Gabon" <?php if($userData['country'] == "Gabon") echo 'selected'; ?>>Gabon</option>

                                                <option value="Gambia" <?php if($userData['country'] == "Gambia") echo 'selected'; ?>>Gambia</option>

                                                <option value="Georgia" <?php if($userData['country'] == "Georgia") echo 'selected'; ?>>Georgia</option>

                                                <option value="Germany" <?php if($userData['country'] == "Germany") echo 'selected'; ?>>Germany</option>

                                                <option value="Ghana" <?php if($userData['country'] == "Ghana") echo 'selected'; ?>>Ghana</option>

                                                <option value="Greece" <?php if($userData['country'] == "Greece") echo 'selected'; ?>>Greece</option>

                                                <option value="Grenada" <?php if($userData['country'] == "Grenada") echo 'selected'; ?>>Grenada</option>

                                                <option value="Guatemala" <?php if($userData['country'] == "Guatemala") echo 'selected'; ?>>Guatemala</option>

                                                <option value="Guinea" <?php if($userData['country'] == "Guinea") echo 'selected'; ?>>Guinea</option>

                                                <option value="Guinea-Bissau" <?php if($userData['country'] == "Guinea-Bissau") echo 'selected'; ?>>Guinea-Bissau</option>

                                                <option value="Guyana" <?php if($userData['country'] == "Guyana") echo 'selected'; ?>>Guyana</option>

                                                <option value="Haiti" <?php if($userData['country'] == "Haiti") echo 'selected'; ?>>Haiti</option>

                                                <option value="Honduras" <?php if($userData['country'] == "Honduras") echo 'selected'; ?>>Honduras</option>

                                                <option value="Hungary" <?php if($userData['country'] == "Hungary") echo 'selected'; ?>>Hungary</option>

                                                <option value="Iceland" <?php if($userData['country'] == "Iceland") echo 'selected'; ?>>Iceland</option>

                                                <option value="India" <?php if($userData['country'] == "India") echo 'selected'; ?>>India</option>

                                                <option value="Indonesia" <?php if($userData['country'] == "Indonesia") echo 'selected'; ?>>Indonesia</option>

                                                <option value="Iran" <?php if($userData['country'] == "Iran") echo 'selected'; ?>>Iran</option>

                                                <option value="Iraq" <?php if($userData['country'] == "Iraq") echo 'selected'; ?>>Iraq</option>

                                                <option value="Ireland" <?php if($userData['country'] == "Ireland") echo 'selected'; ?>>Ireland</option>

                                                <option value="Israel" <?php if($userData['country'] == "Israel") echo 'selected'; ?>>Israel</option>

                                                <option value="Italy" <?php if($userData['country'] == "Italy") echo 'selected'; ?>>Italy</option>

                                                <option value="Jamaica" <?php if($userData['country'] == "Jamaica") echo 'selected'; ?>>Jamaica</option>

                                                <option value="Japan" <?php if($userData['country'] == "Japan") echo 'selected'; ?>>Japan</option>

                                                <option value="Jordan" <?php if($userData['country'] == "Jordan") echo 'selected'; ?>>Jordan</option>

                                                <option value="Kazakhstan" <?php if($userData['country'] == "Kazakhstan") echo 'selected'; ?>>Kazakhstan</option>

                                                <option value="Kenya" <?php if($userData['country'] == "Kenya") echo 'selected'; ?>>Kenya</option>

                                                <option value="Kiribati" <?php if($userData['country'] == "Kiribati") echo 'selected'; ?>>Kiribati</option>

                                                <option value="Kuwait" <?php if($userData['country'] == "Kuwait") echo 'selected'; ?>>Kuwait</option>

                                                <option value="Kyrgyzstan" <?php if($userData['country'] == "Kyrgyzstan") echo 'selected'; ?>>Kyrgyzstan</option>

                                                <option value="Laos" <?php if($userData['country'] == "Laos") echo 'selected'; ?>>Laos</option>

                                                <option value="Latvia" <?php if($userData['country'] == "Latvia") echo 'selected'; ?>>Latvia</option>

                                                <option value="Lebanon" <?php if($userData['country'] == "Lebanon") echo 'selected'; ?>>Lebanon</option>

                                                <option value="Lesotho" <?php if($userData['country'] == "Lesotho") echo 'selected'; ?>>Lesotho</option>

                                                <option value="Liberia" <?php if($userData['country'] == "Liberia") echo 'selected'; ?>>Liberia</option>

                                                <option value="Libya" <?php if($userData['country'] == "Libya") echo 'selected'; ?>>Libya</option>

                                                <option value="Liechtenstein" <?php if($userData['country'] == "Liechtenstein") echo 'selected'; ?>>Liechtenstein</option>

                                                <option value="Lithuania" <?php if($userData['country'] == "Lithuania") echo 'selected'; ?>>Lithuania</option>

                                                <option value="Luxembourg" <?php if($userData['country'] == "Luxembourg") echo 'selected'; ?>>Luxembourg</option>

                                                <option value="Madagascar" <?php if($userData['country'] == "Madagascar") echo 'selected'; ?>>Madagascar</option>

                                                <option value="Malawi" <?php if($userData['country'] == "Malawi") echo 'selected'; ?>>Malawi</option>

                                                <option value="Malaysia" <?php if($userData['country'] == "Malaysia") echo 'selected'; ?>>Malaysia</option>

                                                <option value="Maldives" <?php if($userData['country'] == "Maldives") echo 'selected'; ?>>Maldives</option>

                                                <option value="Mali" <?php if($userData['country'] == "Mali") echo 'selected'; ?>>Mali</option>

                                                <option value="Malta" <?php if($userData['country'] == "Malta") echo 'selected'; ?>>Malta</option>

                                                <option value="Marshall Islands" <?php if($userData['country'] == "Marshall Islands") echo 'selected'; ?>>Marshall Islands</option>

                                                <option value="Mauritania" <?php if($userData['country'] == "Mauritania") echo 'selected'; ?>>Mauritania</option>

                                                <option value="Mauritius" <?php if($userData['country'] == "Mauritius") echo 'selected'; ?>>Mauritius</option>

                                                <option value="Mexico" <?php if($userData['country'] == "Mexico") echo 'selected'; ?>>Mexico</option>

                                                <option value="Micronesia" <?php if($userData['country'] == "Micronesia") echo 'selected'; ?>>Micronesia</option>

                                                <option value="Moldova" <?php if($userData['country'] == "Moldova") echo 'selected'; ?>>Moldova</option>

                                                <option value="Monaco" <?php if($userData['country'] == "Monaco") echo 'selected'; ?>>Monaco</option>

                                                <option value="Mongolia" <?php if($userData['country'] == "Mongolia") echo 'selected'; ?>>Mongolia</option>

                                                <option value="Montenegro" <?php if($userData['country'] == "Montenegro") echo 'selected'; ?>>Montenegro</option>

                                                <option value="Morocco" <?php if($userData['country'] == "Morocco") echo 'selected'; ?>>Morocco</option>

                                                <option value="Mozambique" <?php if($userData['country'] == "Mozambique") echo 'selected'; ?>>Mozambique</option>

                                                <option value="Myanmar" <?php if($userData['country'] == "Myanmar") echo 'selected'; ?>>Myanmar</option>

                                                <option value="Namibia" <?php if($userData['country'] == "Namibia") echo 'selected'; ?>>Namibia</option>

                                                <option value="Nauru" <?php if($userData['country'] == "Nauru") echo 'selected'; ?>>Nauru</option>

                                                <option value="Nepal" <?php if($userData['country'] == "Nepal") echo 'selected'; ?>>Nepal</option>

                                                <option value="Netherlands" <?php if($userData['country'] == "Netherlands") echo 'selected'; ?>>Netherlands</option>

                                                <option value="New Zealand" <?php if($userData['country'] == "New Zealand") echo 'selected'; ?>>New Zealand</option>

                                                <option value="Nicaragua" <?php if($userData['country'] == "Nicaragua") echo 'selected'; ?>>Nicaragua</option>

                                                <option value="Niger" <?php if($userData['country'] == "Niger") echo 'selected'; ?>>Niger</option>

                                                <option value="Nigeria" <?php if($userData['country'] == "Nigeria") echo 'selected'; ?>>Nigeria</option>

                                                <option value="North Macedonia" <?php if($userData['country'] == "North Macedonia") echo 'selected'; ?>>North Macedonia</option>

                                                <option value="Norway" <?php if($userData['country'] == "Norway") echo 'selected'; ?>>Norway</option>

                                                <option value="Oman" <?php if($userData['country'] == "Oman") echo 'selected'; ?>>Oman</option>

                                                <option value="Pakistan" <?php if($userData['country'] == "Pakistan") echo 'selected'; ?>>Pakistan</option>

                                                <option value="Palau" <?php if($userData['country'] == "Palau") echo 'selected'; ?>>Palau</option>

                                                <option value="Panama" <?php if($userData['country'] == "Panama") echo 'selected'; ?>>Panama</option>

                                                <option value="Papua New Guinea" <?php if($userData['country'] == "Papua New Guinea") echo 'selected'; ?>>Papua New Guinea</option>

                                                <option value="Paraguay" <?php if($userData['country'] == "Paraguay") echo 'selected'; ?>>Paraguay</option>

                                                <option value="Peru" <?php if($userData['country'] == "Peru") echo 'selected'; ?>>Peru</option>

                                                <option value="Philippines" <?php if($userData['country'] == "Philippines") echo 'selected'; ?>>Philippines</option>

                                                <option value="Poland" <?php if($userData['country'] == "Poland") echo 'selected'; ?>>Poland</option>

                                                <option value="Portugal" <?php if($userData['country'] == "Portugal") echo 'selected'; ?>>Portugal</option>

                                                <option value="Qatar" <?php if($userData['country'] == "Qatar") echo 'selected'; ?>>Qatar</option>

                                                <option value="Romania" <?php if($userData['country'] == "Romania") echo 'selected'; ?>>Romania</option>

                                                <option value="Russia" <?php if($userData['country'] == "Russia") echo 'selected'; ?>>Russia</option>

                                                <option value="Rwanda" <?php if($userData['country'] == "Rwanda") echo 'selected'; ?>>Rwanda</option>

                                                <option value="Saint Kitts and Nevis" <?php if($userData['country'] == "Saint Kitts and Nevis") echo 'selected'; ?>>Saint Kitts and Nevis</option>

                                                <option value="Saint Lucia" <?php if($userData['country'] == "Saint Lucia") echo 'selected'; ?>>Saint Lucia</option>

                                                <option value="Saint Vincent and the Grenadines" <?php if($userData['country'] == "Saint Vincent and the Grenadines") echo 'selected'; ?>>Saint Vincent and the Grenadines</option>

                                                <option value="Samoa" <?php if($userData['country'] == "Samoa") echo 'selected'; ?>>Samoa</option>

                                                <option value="San Marino" <?php if($userData['country'] == "San Marino") echo 'selected'; ?>>San Marino</option>

                                                <option value="Sao Tome and Principe" <?php if($userData['country'] == "Sao Tome and Principe") echo 'selected'; ?>>Sao Tome and Principe</option>

                                                <option value="Saudi Arabia" <?php if($userData['country'] == "Saudi Arabia") echo 'selected'; ?>>Saudi Arabia</option>

                                                <option value="Senegal" <?php if($userData['country'] == "Senegal") echo 'selected'; ?>>Senegal</option>

                                                <option value="Serbia" <?php if($userData['country'] == "Serbia") echo 'selected'; ?>>Serbia</option>

                                                <option value="Seychelles" <?php if($userData['country'] == "Seychelles") echo 'selected'; ?>>Seychelles</option>

                                                <option value="Sierra Leone" <?php if($userData['country'] == "Sierra Leone") echo 'selected'; ?>>Sierra Leone</option>

                                                <option value="Singapore" <?php if($userData['country'] == "Singapore") echo 'selected'; ?>>Singapore</option>

                                                <option value="Slovakia" <?php if($userData['country'] == "Slovakia") echo 'selected'; ?>>Slovakia</option>

                                                <option value="Slovenia" <?php if($userData['country'] == "Slovenia") echo 'selected'; ?>>Slovenia</option>

                                                <option value="Solomon Islands" <?php if($userData['country'] == "Solomon Islands") echo 'selected'; ?>>Solomon Islands</option>

                                                <option value="Somalia" <?php if($userData['country'] == "Somalia") echo 'selected'; ?>>Somalia</option>

                                                <option value="South Africa" <?php if($userData['country'] == "South Africa") echo 'selected'; ?>>South Africa</option>

                                                <option value="South Korea" <?php if($userData['country'] == "South Korea") echo 'selected'; ?>>South Korea</option>

                                                <option value="South Sudan" <?php if($userData['country'] == "South Sudan") echo 'selected'; ?>>South Sudan</option>

                                                <option value="Spain" <?php if($userData['country'] == "Spain") echo 'selected'; ?>>Spain</option>

                                                <option value="Sri Lanka" <?php if($userData['country'] == "Sri Lanka") echo 'selected'; ?>>Sri Lanka</option>

                                                <option value="Sudan" <?php if($userData['country'] == "Sudan") echo 'selected'; ?>>Sudan</option>

                                                <option value="Suriname" <?php if($userData['country'] == "Suriname") echo 'selected'; ?>>Suriname</option>

                                                <option value="Sweden" <?php if($userData['country'] == "Sweden") echo 'selected'; ?>>Sweden</option>

                                                <option value="Switzerland" <?php if($userData['country'] == "Switzerland") echo 'selected'; ?>>Switzerland</option>

                                                <option value="Syria" <?php if($userData['country'] == "Syria") echo 'selected'; ?>>Syria</option>

                                                <option value="Taiwan" <?php if($userData['country'] == "Taiwan") echo 'selected'; ?>>Taiwan</option>

                                                <option value="Tajikistan" <?php if($userData['country'] == "Tajikistan") echo 'selected'; ?>>Tajikistan</option>

                                                <option value="Tanzania" <?php if($userData['country'] == "Tanzania") echo 'selected'; ?>>Tanzania</option>

                                                <option value="Thailand" <?php if($userData['country'] == "Thailand") echo 'selected'; ?>>Thailand</option>

                                                <option value="Togo" <?php if($userData['country'] == "Togo") echo 'selected'; ?>>Togo</option>

                                                <option value="Tonga" <?php if($userData['country'] == "Tonga") echo 'selected'; ?>>Tonga</option>

                                                <option value="Trinidad and Tobago" <?php if($userData['country'] == "Trinidad and Tobago") echo 'selected'; ?>>Trinidad and Tobago</option>

                                                <option value="Tunisia" <?php if($userData['country'] == "Tunisia") echo 'selected'; ?>>Tunisia</option>

                                                <option value="Turkey" <?php if($userData['country'] == "Turkey") echo 'selected'; ?>>Turkey</option>

                                                <option value="Turkmenistan" <?php if($userData['country'] == "Turkmenistan") echo 'selected'; ?>>Turkmenistan</option>

                                                <option value="Tuvalu" <?php if($userData['country'] == "Tuvalu") echo 'selected'; ?>>Tuvalu</option>

                                                <option value="Uganda" <?php if($userData['country'] == "Uganda") echo 'selected'; ?>>Uganda</option>

                                                <option value="Ukraine" <?php if($userData['country'] == "Ukraine") echo 'selected'; ?>>Ukraine</option>

                                                <option value="United Arab Emirates" <?php if($userData['country'] == "United Arab Emirates") echo 'selected'; ?>>United Arab Emirates</option>

                                                <option value="United Kingdom" <?php if($userData['country'] == "United Kingdom") echo 'selected'; ?>>United Kingdom</option>

                                                <option value="United States" <?php if($userData['country'] == "United States") echo 'selected'; ?>>United States</option>

                                                <option value="Uruguay" <?php if($userData['country'] == "Uruguay") echo 'selected'; ?>>Uruguay</option>

                                                <option value="Uzbekistan" <?php if($userData['country'] == "Uzbekistan") echo 'selected'; ?>>Uzbekistan</option>

                                                <option value="Vanuatu" <?php if($userData['country'] == "Vanuatu") echo 'selected'; ?>>Vanuatu</option>

                                                <option value="Vatican City" <?php if($userData['country'] == "Vatican City") echo 'selected'; ?>>Vatican City</option>

                                                <option value="Venezuela" <?php if($userData['country'] == "Venezuela") echo 'selected'; ?>>Venezuela</option>

                                                <option value="Vietnam" <?php if($userData['country'] == "Vietnam") echo 'selected'; ?>>Vietnam</option>

                                                <option value="Yemen" <?php if($userData['country'] == "Yemen") echo 'selected'; ?>>Yemen</option>

                                                <option value="Zambia" <?php if($userData['country'] == "Zambia") echo 'selected'; ?>>Zambia</option>

                                                <option value="Zimbabwe" <?php if($userData['country'] == "Zimbabwe") echo 'selected'; ?>>Zimbabwe</option>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="col-lg-12">

                                        <div class="inp-wrap small">

                                            <label for="educationExperience">Education Experience <?php echo $required_mark; ?></label>

                                            <textarea class="inp" id="educationExperience" name="educationExperience" <?php echo $is_required; ?>> <?php echo $educationExperience = $userData['education_experience']; ?></textarea>

                                        </div>

                                    </div>

                                    <div class="col-sm-6">

                                        <div class="inp-wrap sm">

                                            <label for="languagesSpoken">Languages Spoken <?php echo $required_mark; ?></label>

                                            <select class="inp" id="languagesSpoken" name="languagesSpoken" <?php echo $is_required; ?>>

                                                <option value="" disabled>Select a language</option>

                                                <option value="English" <?php if($userData['languages_spoken'] == "English") echo 'selected'; ?>>English</option>

                                                <option value="French" <?php if($userData['languages_spoken'] == "French") echo 'selected'; ?>>French</option>

                                                <option value="Spanish" <?php if($userData['languages_spoken'] == "Spanish") echo 'selected'; ?>>Spanish</option>

                                                <option value="German" <?php if($userData['languages_spoken'] == "German") echo 'selected'; ?>>German</option>

                                                <option value="Italian" <?php if($userData['languages_spoken'] == "Italian") echo 'selected'; ?>>Italian</option>

                                                <option value="Chinese" <?php if($userData['languages_spoken'] == "Chinese") echo 'selected'; ?>>Chinese</option>

                                                <option value="Russian" <?php if($userData['languages_spoken'] == "Russian") echo 'selected'; ?>>Russian</option>

                                                <option value="Japanese" <?php if($userData['languages_spoken'] == "Japanese") echo 'selected'; ?>>Japanese</option>

                                                <option value="Portuguese" <?php if($userData['languages_spoken'] == "Portuguese") echo 'selected'; ?>>Portuguese</option>

                                                <option value="Hindi" <?php if($userData['languages_spoken'] == "Hindi") echo 'selected'; ?>>Hindi</option>

                                                <option value="Arabic" <?php if($userData['languages_spoken'] == "Arabic") echo 'selected'; ?>>Arabic</option>

                                                <option value="Bengali" <?php if($userData['languages_spoken'] == "Bengali") echo 'selected'; ?>>Bengali</option>

                                                <option value="Urdu" <?php if($userData['languages_spoken'] == "Urdu") echo 'selected'; ?>>Urdu</option>

                                                <option value="Swahili" <?php if($userData['languages_spoken'] == "Swahili") echo 'selected'; ?>>Swahili</option>

                                                <option value="Indonesian" <?php if($userData['languages_spoken'] == "Indonesian") echo 'selected'; ?>>Indonesian</option>

                                                <option value="Turkish" <?php if($userData['languages_spoken'] == "Turkish") echo 'selected'; ?>>Turkish</option>

                                                <option value="Dutch" <?php if($userData['languages_spoken'] == "Dutch") echo 'selected'; ?>>Dutch</option>

                                                <option value="Swedish" <?php if($userData['languages_spoken'] == "Swedish") echo 'selected'; ?>>Swedish</option>

                                                <option value="Korean" <?php if($userData['languages_spoken'] == "Korean") echo 'selected'; ?>>Korean</option>

                                                <option value="Thai" <?php if($userData['languages_spoken'] == "Thai") echo 'selected'; ?>>Thai</option>

                                                <option value="Greek" <?php if($userData['languages_spoken'] == "Greek") echo 'selected'; ?>>Greek</option>

                                                <option value="Hebrew" <?php if($userData['languages_spoken'] == "Hebrew") echo 'selected'; ?>>Hebrew</option>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="col-sm-6">

                                        <div class="inp-wrap sm">

                                            <label for="nativeLanguage">Teaching Languages <?php echo $required_mark; ?></label>

                                            <select class="inp" id="nativeLanguage" name="nativeLanguage" <?php echo $is_required; ?>>

                                                <option value="English" <?php if($userData['native_language'] == "English") echo 'selected'; ?>>English</option>

                                                <option value="French" <?php if($userData['native_language'] == "French") echo 'selected'; ?>>French</option>

                                                <option value="Spanish" <?php if($userData['native_language'] == "Spanish") echo 'selected'; ?>>Spanish</option>

                                                <option value="German" <?php if($userData['native_language'] == "German") echo 'selected'; ?>>German</option>

                                                <option value="Italian" <?php if($userData['native_language'] == "Italian") echo 'selected'; ?>>Italian</option>

                                                <option value="Chinese" <?php if($userData['native_language'] == "Chinese") echo 'selected'; ?>>Chinese</option>

                                                <option value="Russian" <?php if($userData['native_language'] == "Russian") echo 'selected'; ?>>Russian</option>

                                                <option value="Japanese" <?php if($userData['native_language'] == "Japanese") echo 'selected'; ?>>Japanese</option>

                                                <option value="Portuguese" <?php if($userData['native_language'] == "Portuguese") echo 'selected'; ?>>Portuguese</option>

                                                <option value="Hindi" <?php if($userData['native_language'] == "Hindi") echo 'selected'; ?>>Hindi</option>

                                                <option value="Arabic" <?php if($userData['native_language'] == "Arabic") echo 'selected'; ?>>Arabic</option>

                                                <option value="Bengali" <?php if($userData['native_language'] == "Bengali") echo 'selected'; ?>>Bengali</option>

                                                <option value="Urdu" <?php if($userData['native_language'] == "Urdu") echo 'selected'; ?>>Urdu</option>

                                                <option value="Swahili" <?php if($userData['native_language'] == "Swahili") echo 'selected'; ?>>Swahili</option>

                                                <option value="Indonesian" <?php if($userData['native_language'] == "Indonesian") echo 'selected'; ?>>Indonesian</option>

                                                <option value="Turkish" <?php if($userData['native_language'] == "Turkish") echo 'selected'; ?>>Turkish</option>

                                                <option value="Dutch" <?php if($userData['native_language'] == "Dutch") echo 'selected'; ?>>Dutch</option>

                                                <option value="Swedish" <?php if($userData['native_language'] == "Swedish") echo 'selected'; ?>>Swedish</option>

                                                <option value="Korean" <?php if($userData['native_language'] == "Korean") echo 'selected'; ?>>Korean</option>

                                                <option value="Thai" <?php if($userData['native_language'] == "Thai") echo 'selected'; ?>>Thai</option>

                                                <option value="Greek" <?php if($userData['native_language'] == "Greek") echo 'selected'; ?>>Greek</option>

                                                <option value="Hebrew" <?php if($userData['native_language'] == "Hebrew") echo 'selected'; ?>>Hebrew</option>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="inp-wrap small">

                                        <label for="">Working With <?php echo $required_mark; ?></label>

                                        <ul class="list-inline">

                                            <li>

                                                <label class="custom-check">

                                                    <input type="checkbox" name="workingWith[]" value="Kid" <?= in_array('Kid', $workingWith) ? 'checked' : '' ?> id="workingWithKids">



                                                    <span class="check-mark"></span>

                                                 <?php echo $required_mark; ?></label>

                                                <span class="label">Kids</span>

                                            </li>

                                            <li>

                                                <label class="custom-check">

                                                    <input type="checkbox" name="workingWith[]" value="Youth" <?= in_array('Youth', $workingWith) ? 'checked' : '' ?> id="workingWithYouth">

                                                    <span class="check-mark"></span>

                                                 <?php echo $required_mark; ?></label>

                                                <span class="label">Youth</span>

                                            </li>

                                            <li>

                                                <label class="custom-check">

                                                    <input type="checkbox" name="workingWith[]" value="Adult" <?= in_array('Adult', $workingWith) ? 'checked' : '' ?> id="workingWithAdult">

                                                    <span class="check-mark"></span>

                                                 <?php echo $required_mark; ?></label>

                                                <span class="label">Adult</span>

                                            </li>

                                            <li>

                                                <label class="custom-check">

                                                    <input type="checkbox" name="workingWith[]" value="Groups" <?= in_array('Groups', $workingWith) ? 'checked' : '' ?> id="workingWithGroup">

                                                    <span class="check-mark"></span>

                                                 <?php echo $required_mark; ?></label>

                                                <span class="label">Group</span>

                                            </li>

                                        </ul>

                                    </div>



                                    <div class="inp-wrap small">

                                        <label for="">Levels you teach <?php echo $required_mark; ?></label>

                                        <ul class="list-inline">

                                            <li>

                                                <label class="custom-check">

                                                    <input type="checkbox" name="levelsYouTeach[]" value="Beginner" <?= in_array('Beginner', $levelsYouTeach) ? 'checked' : '' ?> id="teachLevelBeginner">

                                                    <span class="check-mark"></span>

                                                 <?php echo $required_mark; ?></label>

                                                <span class="label">Beginner</span>

                                            </li>

                                            <li>

                                                <label class="custom-check">

                                                    <input type="checkbox" name="levelsYouTeach[]" value="Intermediate" <?= in_array('Intermediate', $levelsYouTeach) ? 'checked' : '' ?> id="teachLevelIntermediate">

                                                    <span class="check-mark"></span>

                                                 <?php echo $required_mark; ?></label>

                                                <span class="label">Intermediate</span>

                                            </li>

                                            <li>

                                                <label class="custom-check">

                                                    <input type="checkbox" name="levelsYouTeach[]" value="Advanced" <?= in_array('Advanced', $levelsYouTeach) ? 'checked' : '' ?> id="teachLevelAdvance">

                                                    <span class="check-mark"></span>

                                                 <?php echo $required_mark; ?></label>

                                                <span class="label">Advance</span>

                                            </li>

                                        </ul>

                                    </div>



                                    <div class="col-sm-4">

                                        <div class="inp-wrap small">

                                            <label for="profilePhoto">Profile Photo <?php echo $required_mark; ?></label>

                                            <div class="upload-field">

                                                <i class="fa-solid fa-file"></i>

                                                <p id="profilePhotoFileName">Upload Your Profile Photo</p>

                                                <input type="file" id="profilePhoto" name="profilePicture" accept=".jpeg, .jpg, .png" <?php echo $is_required; ?>>

                                            </div>

                                        </div>

                                    </div>



                                    <div class="col-sm-4">

                                        <div class="inp-wrap small">

                                            <label for="cv">Upload CV <?php echo $required_mark; ?></label>

                                            <div class="upload-field">

                                                <i class="fa-solid fa-file"></i>

                                                <p id="cvFileName">Upload Your CV</p>

                                                <input type="file" id="cv" name="cv" accept=".pdf, .doc, .docx" <?php echo $is_disabled; ?> <?php echo $is_required; ?>>

                                            </div>

                                            <p> <?php echo $status_text; ?></p>

                                        </div>

                                    </div>



                                    <div class="col-sm-4">

                                        <div class="inp-wrap small">

                                            <label for="officialID">Upload Official ID <?php echo $required_mark; ?></label>

                                            <div class="upload-field">

                                                <i class="fa-solid fa-file"></i>

                                                <p id="officialIDFileName">Upload Your Official ID</p>

                                                <input type="file" id="officialID" name="officialID" accept=".jpeg, .jpg, .png" <?php echo $is_disabled; ?> <?php echo $is_required; ?>>

                                            </div>

                                            <p><?php echo $status_text; ?></p>

                                        </div>

                                    </div>



                                    <div class="inp-wrap small">

                                        <label for="videoIntroduction">Video Introduction</label>

                                        <input class="inp" type="text" id="videoIntroduction" name="videoIntroduction" value=" " />

                                    </div>

                                    <input type="hidden" name="update_tutor_profile" value="1">

                                    <div class="form-btn">

                                        <button class="site-link small grey" type="reset">Reset</button>

                                        <button class="site-link small" type="submit">Confirm</button>

                                    </div>

                                </div> <!-- End of row -->

                            </div>

                        </div>

                    </div>

                </form>



            </div>

        </div>

    </div>



    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="./assets/js/custom.js"></script>

    <script>

        $(document).ready(function() {

            $(".multiple").select2();

        });

    </script>



    <script>

        // Function to update the file name after uploading

        function updateFileName(inputId, fileNameId) {

            const fileInput = document.getElementById(inputId);

            const fileNameDisplay = document.getElementById(fileNameId);



            if (fileInput.files && fileInput.files[0]) {

                fileNameDisplay.textContent = fileInput.files[0].name;

            } else {

                fileNameDisplay.textContent = "Upload Your File";

            }

        }



        // Add event listeners to update file names after uploading

        document.getElementById("cv").addEventListener("change", function() {

            updateFileName("cv", "cvFileName");

        });



        document.getElementById("profilePhoto").addEventListener("change", function() {

            updateFileName("profilePhoto", "profilePhotoFileName");

        });



        document.getElementById("officialID").addEventListener("change", function() {

            updateFileName("officialID", "officialIDFileName");

        });

    </script>

</body>



</html>