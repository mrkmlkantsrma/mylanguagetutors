<?php
$page_title = 'MyLanguageTutor : Courses';
$page_description = '';

require_once 'header.php';
?>

    <section class="course-listing">
        <div class="container">

            <div class="avl-language">
                <a class="site-link sm green" href="?">All</a>
                <a class="site-link sm" href="?language=english">English</a>
                <a class="site-link sm" href="?language=french">French</a>
                <a class="site-link sm" href="?language=spanish">Spanish</a>
                <a class="site-link sm" href="?language=other">Other Languages</a>
            </div>

            <div class="row tutList">
    <?php foreach ($tutors as $tutor): ?>
        <div class="col-sm-6 col-lg-3">
            <div class="course-single">
                <div class="course-single-img">
                    <?php
                    $profilePicture = $tutor['profile_photo_filepath'];
                    $link = "course-details?username=" . $tutor['username'] . "&languages=" . urlencode($tutor['languages_spoken']);

                    echo '<a href="' . $link . '">';

                    if (isset($profilePicture) && !empty($profilePicture)) {
                        $profilePicture = str_replace('../../', '', $profilePicture);
                        $profilePictureUrl = '/' . $profilePicture;
                        echo "<img src='" . $profilePictureUrl . "' alt='" . $tutor['username'] . "' />";
                    } else {
                        echo "<img src='https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg' alt='" . $tutor['username'] . "' />";
                    }

                    echo '</a>';
                    ?>
                </div>
                <div class="course-single-right">
                    <div class="course-single-title">
                        <h5>
                            <a href="course-details?username=<?= $tutor['username'] ?>&languages=<?= urlencode($tutor['languages_spoken']) ?>">
                                <?= $tutor['username'] ?>
                            </a>
                        </h5>
                        <div class="ratings">
                            <p> <strong><?= number_format($tutor['average_rating'] ?? 0, 1) ?></strong> <i class="fa-solid fa-star"></i></p>
                        </div>
                    </div>
                    <span class="tut-location"><i class="fa-solid fa-location-dot"></i> <?= $tutor['country'] ?></span>
                    <div class="tut-meta">
                        <ul>
                            <li>
                                <div class="meta-icon"><i class="fa-regular fa-flag"></i></div>
                                <div class="meta-txt">
                                    <p><span>Teaching Language</span> <br> <?= $tutor['languages_spoken'] ?></p>
                                </div>
                            </li>
                            <li>
                                <div class="meta-icon"><i class="fa-regular fa-map"></i></div>
                                <div class="meta-txt">
                                    <p><span>Teaching Languages</span> <br><?= $tutor['native_language'] ?></p>
                                </div>
                            </li>
                            <li>
                                <div class="meta-icon"><i class="fa-regular fa-user"></i></div>
                                <div class="meta-txt">
                                    <p><span>Teaching Level</span> <br>
                                        <?php
                                        if (isset($tutor['levels_you_teach']) && !is_null($tutor['levels_you_teach'])) {
                                            $teachingLevels = explode(',', $tutor['levels_you_teach']);
                                            echo implode(", ", $teachingLevels);
                                        } ?>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <div class="meta-icon"><i class="fa-solid fa-calendar-days"></i></div>
                                <div class="meta-txt">
                                    <p><span>Available</span> <br>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <div class="meta-icon"><i class="fa-solid fa-video"></i></div>
                                <div class="meta-txt">
                                    <p><span>Platform</span> <br>
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

        </div>
    </section>
    <?php require_once 'footer.php'; ?>
</body>

</html>