
<?php 
if(session_status() == PHP_SESSION_NONE) { session_start(); }

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$pathroot = __DIR__; 
require_once 'src/controllers/UserController.php';
require_once $pathroot . '/src/models/User.php';
$user = new User();

$language = $_GET['language'] ?? null;
$tutors = $user->getTutorsData($language); 

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no" />
        <link rel="shortcut icon" href="assets/images/favicon.png" />
        <title><?php echo $page_title; ?></title>
        <meta name="description" content="<?php echo $page_description; ?>"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
        <link rel="stylesheet" href="assets/css/custom.css" />
        <link rel="stylesheet" href="assets/css/group-classes-style.css" />
        <script type="application/ld+json">
            {
                "@context": "http://schema.org",
                "@type": "LocalBusiness",
                "name": "MyLanguageTutor",
                "telephone": "+1 (514) 547-1551",
                "address": {
                    "@type": "PostalAddress",
                    "addressLocality": "Gatineau, Québec",
                    "addressCountry": "Canada"
                },
                "email": "mylanguagetutor01@gmail.com",
                "logo": "https://mylanguagetutor.ca/assets/images/logo.png",
                "url": "https://mylanguagetutor.ca"
            }
        </script>
        <script type="text/javascript">
            function googleTranslateElementInit() { new google.translate.TranslateElement({}, "google_translate_element"); }
            function triggerTranslation(lang) { var googleTranslator = document.getElementsByClassName("goog-te-combo")[0]; if (lang !== "selectLanguage" && googleTranslator) { googleTranslator.value = lang; googleTranslator.dispatchEvent(new Event("change"));}}
        </script>
        <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
        <xhtml:link rel="alternate" hreflang="en" href="https://mylanguagetutor.ca/" />
        <xhtml:link rel="alternate" hreflang="fr" href="https://mylanguagetutor.ca/fr/" />
        <xhtml:link rel="alternate" hreflang="es" href="https://mylanguagetutor.ca/es/" />
    </head>
    <body>
        <header class="site-header">
            <div class="container-fluid">
                <div class="site-header-in">
                    <div class="logo">
                        <a href="/"><img src="./assets/images/logo.png" alt="" /></a>
                        <div class="mobClick">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="header-right">
                        <div class="header-right-top">
                            <ul>
                                <li class="active" data-lang="en" onclick="triggerTranslation('en')">
                                    <span><img src="./assets/images/en.svg" alt="" /></span> English
                                </li>
                                <li data-lang="fr" onclick="triggerTranslation('fr')">
                                    <span><img src="./assets/images/fr.svg" alt="" /></span> French
                                </li>
                                <li data-lang="es" onclick="triggerTranslation('es')">
                                    <span><img src="./assets/images/es.svg" alt="" /></span> Español
                                </li>
                                <li class="selectLanguage" onclick="triggerTranslation('selectLanguage')">Other Language</li>
                            </ul>
                        </div>
                        <div id="google_translate_element"></div>

                        <div class="header-right-bottom">
                            <div class="site-nav">
                                <ul>
                                    <li><a href="/">About us</a></li>
                                    <li><a href="find-tutor">Find a Tutor</a></li>
                                    <li><a href="group-classes">Group Classes</a></li>
                                    <?php if(!empty($_SESSION['user_id'])) { ?>
                                        <?php if($_SESSION['role'] == 'Student'){ ?>
                                            <li><a href="/src/views/student/my-lessons">Enroll</a></li>
                                        <?php }else if($_SESSION['role'] == 'Tutor'){ ?>
                                            <li><a href="/src/views/tutor/my-lessons">Enroll</a></li>
                                        <?php } ?>
                                    <?php }else{ ?>
                                        <li><a href="register">Enroll</a></li>
                                    <?php } ?>
                                    <li><a href="fees">Fees</a></li>
                                    <li><a href="resources">Resources</a></li>
                                    <li><a href="contacts">Contacts</a></li>
                                </ul>
                            </div>
                            <div class="header-btn">
                                <?php if(!empty($_SESSION['user_id'])) { ?>
                                    <?php if($_SESSION['role'] == 'Student'){ ?>
                                        <a class="site-link" href="/src/views/student/profile">Hi, <?php echo $_SESSION['username']; ?></a>
                                    <?php }else if($_SESSION['role'] == 'Tutor'){ ?>
                                        <a class="site-link" href="/src/views/tutor/profile">Hi, <?php echo $_SESSION['username']; ?></a>
                                    <?php }else if($_SESSION['role'] == 'Admin'){ ?>
                                        <a class="site-link" href="/src/views/admin/overview">Hi, <?php echo $_SESSION['username']; ?></a>
                                    <?php }?>
                                <?php }else{ ?>
                                    <a class="site-link" href="login">Login</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>