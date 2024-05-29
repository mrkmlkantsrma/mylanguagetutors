<?php

$page_title = 'Find Expert Language Tutors Online | MyLanguageTutor';
$page_description = 'Discover expert language tutors online at MyLanguageTutor. Join us to master a new language affordably and effectively. Explore tutors for English, French, Spanish, and more.';

require_once 'header.php';

require_once 'src/models/groupClass.php';

$groupClass = new GroupClass();
$groupClasses = $groupClass->getAllGroupClasses();

function convertTo12HourFormat($time) {
    $dateTime = new DateTime($time);
    return $dateTime->format('g:i A');
}

?>
        <section class="course-listing">
            <div class="container">
                <div class="group-classes-header">
                    <h2>Our Group Classes</h2>
                    <p>Join like-minded language learners in our curated group classes. Whether you're a beginner ence the joy of collaborative learning in our group classes. Engage in stimulating discussions, share perspectives, and enhance your language skills in a dynamic setting.</p>
                </div>
                <div class="row groupClassList">
                    <?php if (empty($groupClasses)): ?>
                        <div class="col-12">
                            <p>No group classes now. Please check back later.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($groupClasses as $class): ?>
                            <div class="col-sm-6 col-lg-3 group-class-card" data-class-id="<?= htmlspecialchars($class['class_id']) ?>"> <!-- Added data-class-id attribute here -->
                                <div class="group-class-single">
                                    <div class="group-class-img">
                                        <?php if(!empty($_SESSION['user_id'])) { ?>
                                            <?php if($_SESSION['role'] == 'Student'){ ?>
                                                <a href="src/views/student/group-classes.php?highlight=<?= htmlspecialchars($class['class_id']) ?>"><img src="<?= !empty($class['cover_image_path']) ? '/uploads/groupClasses/' . htmlspecialchars($class['cover_image_path']) : './assets/images/tutor-3.jpg' ?>" alt="<?= htmlspecialchars($class['title']) ?>" /></a>
                                            <?php }else if($_SESSION['role'] == 'Tutor'){ ?>
                                                <a href="/src/views/tutor/my-lessons.php"><img src="<?= !empty($class['cover_image_path']) ? '/uploads/groupClasses/' . htmlspecialchars($class['cover_image_path']) : './assets/images/tutor-3.jpg' ?>" alt="<?= htmlspecialchars($class['title']) ?>" /></a>
                                            <?php } ?>
                                        <?php }else{ ?>
                                            <a href="src/views/student/group-classes.php?highlight=<?= htmlspecialchars($class['class_id']) ?>"><img src="<?= !empty($class['cover_image_path']) ? '/uploads/groupClasses/' . htmlspecialchars($class['cover_image_path']) : './assets/images/tutor-3.jpg' ?>" alt="<?= htmlspecialchars($class['title']) ?>" />z</a>
                                        <?php } ?>
                                    </div>
                                    <div class="group-class-details">
                                        <h5>
                                        <?php if(!empty($_SESSION['user_id'])) { ?>
                                            <?php if($_SESSION['role'] == 'Student'){ ?>
                                                <a href="src/views/student/group-classes.php?highlight=<?= htmlspecialchars($class['class_id']) ?>"><?= htmlspecialchars($class['title']) ?></a>
                                            <?php }else if($_SESSION['role'] == 'Tutor'){ ?>
                                                <a href="/src/views/tutor/my-lessons.php"><?= htmlspecialchars($class['title']) ?></a>
                                            <?php } ?>
                                        <?php }else{ ?>
                                            <a href="src/views/student/group-classes.php?highlight=<?= htmlspecialchars($class['class_id']) ?>"><?= htmlspecialchars($class['title']) ?></a>
                                        <?php } ?>
                                        </h5>
                                        <p><?= htmlspecialchars($class['description']) ?></p>
                                        
                                        <div class="class-info">
                                            <p><i class="fa-solid fa-calendar"></i> Schedules: 
                                                <?= htmlspecialchars($class['first_day']) ?> <?= convertTo12HourFormat($class['first_time']) ?>, 
                                                <?= htmlspecialchars($class['second_day']) ?> <?= convertTo12HourFormat($class['second_time']) ?> Weekly
                                            </p>
                                            <p><i class="fa-solid fa-globe-americas"></i> Timezone: Eastern Time</p>
                                            <p><i class="fa-solid fa-hashtag"></i> Number of Classes: <?= htmlspecialchars($class['number_of_classes']) ?></p>
                                            <p><i class="fa-solid fa-clock"></i> Duration: <?= htmlspecialchars($class['duration']) ?> mins</p>
                                        </div>
                                        <div class="price-enroll">
                                            <p>Price: $<?= htmlspecialchars($class['pricing']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <br>
                <div class="row">
                    <div class="col-12 text-center">
                    <?php if(!empty($_SESSION['user_id'])) { ?>
                        <?php if($_SESSION['role'] == 'Student'){ ?>
                            <a href="src/views/student/group-classes.php" class="site-link">Get Group Classes</a>
                        <?php }else if($_SESSION['role'] == 'Tutor'){ ?>
                            <a href="/src/views/tutor/my-lessons.php" class="site-link">Get Group Classes</a>
                        <?php } ?>
                    <?php }else{ ?>
                        <a href="src/views/student/group-classes.php" class="site-link">Login to Get started</a>
                    <?php } ?>
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
                                <img src="./assets/images/black-student.webp" alt="Group class interaction graphic" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="image-block-right">
                                <span class="title-small">Group Classes</span>
                                <h2>Experience Collaborative Learning</h2>
                                <p>Group classes provide an interactive environment where you can practice the language with peers, share experiences, and learn from multiple perspectives.</p>
                                <p>Our dedicated tutors facilitate lively discussions and group activities to make your learning journey enjoyable and impactful.</p>
                                <?php if(!empty($_SESSION['user_id'])) { ?>
                                    <?php if($_SESSION['role'] == 'Student'){ ?>
                                        <a class="site-link mt-4" href="/src/views/student/group-classes.php">Join a Group Class</a>
                                    <?php }else if($_SESSION['role'] == 'Tutor'){ ?>
                                        <a class="site-link mt-4" href="/src/views/tutor/my-lessons.php">Join a Group Class</a>
                                    <?php } ?>
                                <?php }else{ ?>
                                    <a class="site-link mt-4" href="register.php?type=student">Join a Group Class</a>
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
                                <img src="./assets/images/video-call.jpg" alt="Digital group class platform graphic" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="image-block-right">
                                <span class="title-small">High Quality Tools</span>
                                <h2 class="green">Digital Group Classrooms</h2>
                                <p>We utilize state-of-the-art digital platforms tailored for group classes. Engage in fun polls, collaborative projects, and breakout discussions.</p>
                                <p>Every tool is designed to enhance your group learning experience and ensure effective communication.</p>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- <section class="how-it-works">
            <div class="container">
                <div class="section-header">
                    <h2>How It Works</h2>
                    <p>Joining our platform and finding your perfect language class is simple. Here's how:</p>
                </div>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="step">
                            <div class="step-icon">
                                <img src="./assets/images/icon-signup.png" alt="Sign Up Icon">
                            </div>
                            <h4>Step 1: Sign Up</h4>
                            <p>Enroll on our platform for free to get started. Choose if you're a student or a tutor.</p>
                        </div>
                    </div>


                    <div class="col-lg-3">
                        <div class="step">
                            <div class="step-icon">
                                <img src="./assets/images/icon-choose.png" alt="Choose Class Icon">
                            </div>
                            <h4>Step 2: Choose Your Class</h4>
                            <p>Browse through our wide range of group classes or connect with individual tutors.</p>
                        </div>
                    </div>


                    <div class="col-lg-3">
                        <div class="step">
                            <div class="step-icon">
                                <img src="./assets/images/icon-attend.png" alt="Attend Session Icon">
                            </div>
                            <h4>Step 3: Attend Sessions</h4>
                            <p>Once enrolled, you'll receive details for your sessions. Attend and learn!</p>
                        </div>
                    </div>


                    <div class="col-lg-3">
                        <div class="step">
                            <div class="step-icon">
                                <img src="./assets/images/icon-feedback.png" alt="Give Feedback Icon">
                            </div>
                            <h4>Step 4: Give Feedback</h4>
                            <p>After your session, leave a review for your tutor and help our community grow.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->

        <section class="testimonials">
            <div class="container">
                <div class="testimonials-main" data-aos="fade-up">
                    <h2 class="title">What Group Class Participants are Saying</h2>
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
                                            The group class was such an enriching experience for me. I was not only learning from the tutor but also from my peers. The interactive sessions, combined with the digital tools, made each class exciting and memorable.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- Add more unique testimonials related to group classes here -->
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
        </script>
    </body>
</html>
