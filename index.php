<?php
$page_title = 'Find Expert Language Tutors Online in Canada | MyLanguageTutor';
$page_description = 'Find the best language tutors in Canada with MyLanguageTutor. Join us to learn French, English, Spanish and more. Master a second language affordably and effectively.';

require_once 'header.php';

require_once $pathroot . '/vendor/google-api/Google_Client.php';
require_once $pathroot . '/vendor/google-api/contrib/Google_Oauth2Service.php';
require_once $pathroot . '/src/config/Database.php';

$clientId = $_ENV['GOOGLE_CLIENT_ID'];
$clientSecret = $_ENV['GOOGLE_SECRET_KEY'];
$redirectURL = $_ENV['GOOGLE_REDIRECT_URL'];

$gClient = new Google_Client();
$gClient->setApplicationName('Login Using Google');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectURL);
$google_oauthV2 = new Google_Oauth2Service($gClient);

$database = new Database();
$conn = $database->dbConnection();

?>
        <section class="welcome-txt">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-5">
                        <div class="welcome-txt-left">
                            <h1>Connect with Expert Language Tutors in Canada</h1>
                            <p>My Language Tutor brings together qualified tutors and learners eager to speak their desired language at an affordable rate.</p>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="welcome-txt-right">
                            <h2>Explore Top Tutors by Language</h2>
                            <div class="select-lang">
                                <ul>
                                    <li>
                                        <a href="find-tutor?language=english">
                                            <div class="select-lang-img"><img src="./assets/images/en.svg" alt="Find English tutors icon" /></div>
                                            English
                                        </a>
                                    </li>
                                    <li>
                                        <a href="find-tutor?language=french">
                                            <div class="select-lang-img"><img src="./assets/images/fr.svg" alt="Find French tutors icon" /></div>
                                            French
                                        </a>
                                    </li>
                                    <li>
                                        <a href="find-tutor?language=spanish">
                                            <div class="select-lang-img"><img src="./assets/images/es.svg" alt="Find Spanish tutors icon" /></div>
                                            Spanish
                                        </a>
                                    </li>
                                    <li>
                                        <a href="find-tutor?language=other">
                                            <div class="select-lang-img"><img src="./assets/images/other.png" alt="Find tutors for other languages icon" /></div>
                                            Other Languages
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="img-single">
            <img src="./assets/images/mylang.jpg" alt="Image depicting the joy of language learning with MyLanguageTutor" />
        </section>

        <section class="image-block">
            <div class="image-block-single">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="image-block-left">
                                <img src="./assets/images/img-2.png" alt="A tutor guiding a student, showcasing personal language tutoring" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="image-block-right">
                                <span class="title-small">Personalized Learning</span>
                                <h2>Discover MyLanguageTutor</h2>
                                <p>We are Canada's leading platform for second language tutoring, bridging the gap between passionate language tutors and enthusiastic learners.</p>
                                <p>We believe in making language learning accessible to everyone, offering competitive rates for students and fair compensation for our tutors.</p>
                                <p>My Language Tutor is registered in the province of Québec under this legal name: 9488-5381 Québec inc.</p>
                                <?php if(!empty($_SESSION['user_id'])) { ?>
                                    <?php if($_SESSION['role'] == 'Student'){ ?>
                                        <a class="site-link mt-4" href="/src/views/student/my-lessons">Become a Tutor Today</a>
                                    <?php }else if($_SESSION['role'] == 'Tutor'){ ?>
                                        <a class="site-link mt-4" href="/src/views/tutor/my-lessons">Become a Tutor Today</a>
                                    <?php } ?>
                                <?php }else{ ?>
                                    <a class="site-link mt-4" href="register.php?type=tutor">Become a Tutor Today</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="image-block-single">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="image-block-left">
                                <img src="./assets/images/img-3.png" alt="Interactive online learning tools, representing modern language learning methods" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="image-block-right">
                                <span class="title-small">Excellence in Language Training</span>
                                <h2 class="green">Embark on Your Language Journey</h2>
                                <p>With MyLanguageTutor, step into a world of innovative language learning. Our diverse courses ensure a tailored experience, helping you speak your desired language confidently.</p>
                                <?php if(!empty($_SESSION['user_id'])) { ?>
                                    <?php if($_SESSION['role'] == 'Student'){ ?>
                                        <a class="site-link green mt-4" href="/src/views/student/my-lessons">Start Learning Now</a>
                                    <?php }else if($_SESSION['role'] == 'Tutor'){ ?>
                                        <a class="site-link green mt-4" href="/src/views/tutor/my-lessons">Start Learning Now</a>
                                    <?php } ?>
                                <?php }else{ ?>
                                    <a class="site-link green mt-4" href="register.php?type=student">Start Learning Now</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="image-block">
            <div class="image-block-single">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="image-block-left">
                                <img src="./assets/images/video-call.jpg" alt="Diverse group of students actively participating in a class" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="image-block-right">
                                <span class="title-small">Join Our Group Classes</span>
                                <h2>Unlock Collaborative Learning</h2>
                                <p>Step into our group classes and immerse yourself in an engaging learning environment. Practice with peers, share stories, and learn from various perspectives.</p>
                                <p>Guided by our dedicated tutors, indulge in stimulating discussions and group tasks that make your learning more than just textbook knowledge.</p>
                                <a class="site-link mt-4" href="group-classes.php">Discover Group Classes</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="find-tutors">
            <div class="container">
                <div class="find-tutors-main">
                    <h2 class="text-center">Our Featured Tutors</h2>
                    <div class="tutor-search">
                        <div class="row">
                            <?php 
                            // Check if tutors exist and then display the top 4
                            if (isset($tutors) && is_array($tutors) && count($tutors) > 0): 
                                $topTutors = array_slice($tutors, 0, 4); // Get top 4 tutors
                                foreach ($topTutors as $tutor): 
                            ?>
                            <div class="col-sm-6 col-lg-3">
                                <div class="tutors-single">
                                    <div class="tutor-img">
                                        <?php
                                        $profilePicture = $tutor['profile_photo_filepath'] ?? 'https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg';
                                        $profilePicture = str_replace('../../', '', $profilePicture);
                                        $altText = "Profile picture of " . $tutor['username'];
                                        echo "<img src='" . $profilePicture . "' alt='" . $altText . "' />";
                                        ?>
                                    </div>
                                    <div class="tutor-txt">
                                        <h5>
                                            <a href="course-details?username=<?= $tutor['username'] ?>&languages=<?= urlencode($tutor['languages_spoken']) ?>">
                                                <?= $tutor['username'] ?>
                                            </a>
                                        </h5>
                                        <ul>
                                            <li><?= $tutor['country'] ?></li>
                                            <li><?= $tutor['languages_spoken'] ?></li>
                                            <li>Available</li>
                                            <li>
                                                <?php
                                                $teachingLevels = explode(',', $tutor['levels_you_teach'] ?? '');
                                                echo implode(", ", $teachingLevels);
                                                ?>
                                            </li>
                                        </ul>
                                        <a class="site-link full mt-4" href="course-details?username=<?= $tutor['username'] ?>&languages=<?= urlencode($tutor['languages_spoken']) ?>">View Profile</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="testimonials">
            <div class="container">
                <div class="testimonials-main" data-aos="fade-up">
                    <h2 class="title">Hear From Our Satisfied Learners</h2>
                    <div class="list-testimonials">
                        <div class="owl-carousel testi-carousel">
                            <div class="item">
                                <div class="testi-single">
                                    <div class="testi-left">
                                        <div class="testi-img"><img src="./assets/images/testi-img.png" alt="Portrait of Jasmine Pedraza" /></div>
                                        <h6>JASMINE PEDRAZA</h6>
                                    </div>
                                    <div class="testi-right">
                                        <p>
                                            My SPEAK journey was transformational. Apart from being affordable, the intimate group setting made each session special. By the end, the group felt like family. SPEAK has been a casual yet effective approach to enhancing my language skills.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="testi-single">
                                    <div class="testi-left">
                                        <div class="testi-img"><img src="./assets/images/testi-img.png" alt="Portrait of Alex Carter" /></div>
                                        <h6>ALEX CARTER</h6>
                                    </div>
                                    <div class="testi-right">
                                        <p>
                                            SPEAK has been a game-changer for me. The tutors are dedicated, and their methods are unique and effective. I've never felt more confident in my linguistic abilities.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="hero-banner">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="banner-txt">
                            <h1>Your Gateway to Global Communication: Speak, Connect, Thrive.</h1>
                            <a class="site-link mt-4" href="find-tutor.php">Begin Your Journey</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="banner-img">
                            <img src="./assets/images/img-1.png" alt="Lively classroom setting with students engaging" />
                            <div class="infoTxt">
                                <div class="infoTxt-img"><img src="./assets/images/education.png" alt="Education icon symbolizing learning platform" /></div>
                                <div class="infoTxt-cont">
                                    <p>Leading Online Learning Platform</p>
                                </div>
                            </div>
                            <div class="infoTxt pos-2">
                                <div class="infoTxt-img"><img src="./assets/images/students.png" alt="Students icon representing learners" /></div>
                                <div class="infoTxt-cont">
                                    <p>Empowering 100k Students Daily</p>
                                </div>
                            </div>
                            <div class="infoTxt pos-3">
                                <div class="infoTxt-img"><img src="./assets/images/purpose.png" alt="Purpose icon indicating mission" /></div>
                                <div class="infoTxt-cont">
                                    <p>Home to Over 1 Million Expert Tutors</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php require_once 'footer.php'; ?>
        <script>
            var owl = $(".testi-carousel");
            owl.owlCarousel({
                smartSpeed: 1000,
                items: 1,
                margin: 0,
                nav: true,
                navText: ["<i class='fa-solid fa-arrow-left-long'></i>", "<i class='fa-solid fa-arrow-right-long'></i>"],
                dots: true,
            });

            jQuery(document).ready(function() {
                    if (window.location.search.indexOf('code=') !== -1) {
                        var baseUrl = window.location.origin + window.location.pathname;
                        window.location.href = baseUrl;
                    }
            });
        </script>
    </body>
</html>

<?php
/********************* SingUp with Google Sec *******************************/ 
if($_SESSION['requested_page'] == '/login.php' || $_SESSION['requested_page'] == '/login' || $_SESSION['requested_page'] == '/register.php' || $_SESSION['requested_page'] == '/register'){
    

    ?><script>
    jQuery(document).ready(function() {
        if (window.location.search.indexOf('code=') == -1) {
            var baseUrl = window.location.origin + window.location.pathname;
            var role = '<?php echo $_SESSION['role']; ?>';
            if(role == 'Student'){
                window.location.href = baseUrl + 'src/views/student/profile';
            }else if(role == 'Tutor'){
                window.location.href = baseUrl + 'src/views/tutor/profile';
            }else{
                window.location.href = baseUrl;
            }
        }
    });
    </script><?php
    if( $_SESSION['user_id']){
        $_SESSION['requested_page'] = '';
    }
}

if(isset($_GET['code']))
{
    $gClient->authenticate($_GET['code']);

    $_SESSION['token'] = $gClient->getAccessToken(); 
}

if (isset($_SESSION['token'])) 
{
    $gClient->setAccessToken($_SESSION['token']);
}
if ($gClient->getAccessToken()) 
{
 
    $gpUserProfile = $google_oauthV2->userinfo->get();

    $google_id  = $gpUserProfile['id'];
    $email      = $gpUserProfile['email'];
    $full_name  = $gpUserProfile['name'];
    $first_name = $gpUserProfile['given_name'];
    $last_name  = $gpUserProfile['family_name'];
    $user_img   = $gpUserProfile['picture'];
    $verify_email   = $gpUserProfile['verified_email'];

    $check_email = $user->emailExists($email);

        if($check_email == 1)
        { 
            $userRole = $user->getUserRoleByEmail($email);
            $userID = $user->getIdByEmail($email);
            $_SESSION['user_id'] = $userID;
            $_SESSION['username'] = $full_name;
            $_SESSION['role'] = $userRole;
            $_SESSION['userData'] = array(
                'google_id'  => $google_id,
                'email'      => $email,
                'full_name'  => $full_name,
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'user_img'   => $user_img,
                'userRole'   => $userRole,
                'verify_email'=> $verify_email
            );

            // Retrieve user data from the session
            $userData = $_SESSION['userData'];
        }
        else
        {
            if($_SESSION['requested_page'] == '/login.php' || $_SESSION['requested_page'] == '/login'){
                unset($_SESSION['token']); ?>
                <script>
                    jQuery(document).ready(function() {
                        var baseUrl = window.location.origin + window.location.pathname;
                        window.location.href = baseUrl + 'register';
                    });
                </script>
                <?php
            }else if($_SESSION['requested_page'] == '/register.php' || $_SESSION['requested_page'] == '/register'){

                $result = $user->UserLogin($full_name, $first_name, $last_name, $email, $google_id, $_SESSION['role'], $verify_email, $user_img );

                $_SESSION['user_id'] = $result;

                $_SESSION['username'] = $full_name;
                $_SESSION['userData'] = array(
                    'google_id'  => $google_id,
                    'email'      => $email,
                    'full_name'  => $full_name,
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                    'user_img'   => $user_img,
                    'userRole'   => $_SESSION['role'],
                    'verify_email'=> $verify_email
                );
                $_SESSION['success'] = 'Registration Successful.';
            }
        }
    header('Location: ' . filter_var($redirectURL, FILTER_SANITIZE_URL));
}


/**************** Singup with Google Sec End  *************************/ 


if(isset($accessToken)){
    if(isset($_SESSION['facebook_access_token'])){
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    }else{
        // Put short-lived access token in session
        $_SESSION['facebook_access_token'] = (string) $accessToken;
        
          // OAuth 2.0 client handler helps to manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();
        
        // Exchanges a short-lived access token for a long-lived one
        $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
        $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
        
        // Set default access token to be used in script
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    }
    
    // Redirect the user back to the same page if url has "code" parameter in query string
    if(isset($_GET['code'])){
        header('Location: ./');
    }
    
    // Getting user facebook profile info
    try {
        $profileRequest = $fb->get('/me?fields=name,first_name,last_name,email,link,gender,locale,picture');
        $fbUserProfile = $profileRequest->getGraphNode()->asArray();
    } catch(FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        session_destroy();
        // Redirect user back to app login page
        header("Location: ./");
        exit;
    } catch(FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    
    // Initialize User class
    $user = new User();
    
    // Insert or update user data to the database
    $fbUserData = array(
        'oauth_provider'=> 'facebook',
        'oauth_uid'     => $fbUserProfile['id'],
        'first_name'    => $fbUserProfile['first_name'],
        'last_name'     => $fbUserProfile['last_name'],
        'email'         => $fbUserProfile['email'],
        'gender'        => $fbUserProfile['gender'],
        'locale'        => $fbUserProfile['locale'],
        'picture'       => $fbUserProfile['picture']['url'],
        'link'          => $fbUserProfile['link']
    );
    $userData = $user->checkUser($fbUserData);
    
    // Put user data into session
    $_SESSION['userData'] = $userData;
    
    // Get logout url
    $logoutURL = $helper->getLogoutUrl($accessToken, $redirectURL.'logout.php');
    
    // Render facebook profile data
    if(!empty($userData)){
        $output  = '<h1>Facebook Profile Details </h1>';
        $output .= '<img src="'.$userData['picture'].'">';
        $output .= '<br/>Facebook ID : ' . $userData['oauth_uid'];
        $output .= '<br/>Name : ' . $userData['first_name'].' '.$userData['last_name'];
        $output .= '<br/>Email : ' . $userData['email'];
        $output .= '<br/>Gender : ' . $userData['gender'];
        $output .= '<br/>Locale : ' . $userData['locale'];
        $output .= '<br/>Logged in with : Facebook';
        $output .= '<br/><a href="'.$userData['link'].'" target="_blank">Click to Visit Facebook Page</a>';
        $output .= '<br/>Logout from <a href="'.$logoutURL.'">Facebook</a>'; 
    }else{
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
    }
    
}
?>

