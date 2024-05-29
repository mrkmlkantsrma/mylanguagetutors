<?php

$page_title = 'MyLanguageTutor : Course Details';
$page_description = '';

global $user;

require_once 'header.php';

$username = $_GET['username'];

// Fetch the tutor's details
$tutor = $user->getTutorByUsername($username);

if ($tutor === null) {
    die("No tutor found with username {$username}");
}

$averageRating = $tutor['average_rating'];
$reviews = $tutor['reviews'];
?>
    <section class="course-listing">
    <div class="container">
       <div class="course-details">
        <div class="row">
            <div class="col-lg-8">
                <div class="course-details-left">
                    <div class="course-details-top">
                      <div class="course-single-img">
                          <?php
                          $profilePicture = $tutor['profile_photo_filepath'];
                          if (isset($profilePicture) && !empty($profilePicture)) {
                              $profilePicture = str_replace('../../', '', $profilePicture);
                              $profilePictureUrl = '/' . $profilePicture;
                              echo "<div class='course-single-img'><img src='" . $profilePictureUrl . "' alt='" . $tutor['username'] . "' /></div>";
                          } else {
                              echo "<div class='course-single-img'><img src='https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg' alt='" . $tutor['username'] . "' /></div>";
                          }
                          ?>
                      </div>
                      <div class="course-single-right">
                          <div class="course-single-title">
                              <h5><?php echo $tutor['username']; ?></h5>
                          </div>
                          <span class="tut-location"><i class="fa-solid fa-location-dot"></i> <?php echo $tutor['country']; ?></span>
                          <div class="tutor-desc">
                              <p>
                                  <strong>Education /Professional Experience</strong> -
                                  <?php echo $tutor['education_experience']; ?>
                              </p>
                          </div>
                      </div>
                    </div>
                    <div class="course-details-bottom">
                        <div class="tut-meta">
                            <ul>
                                <!-- <li>
                                    <div class="meta-icon"><i class="fa-solid fa-earth-americas"></i></div>
                                    <div class="meta-txt">
                                        <p><span>City</span> <br> Dhaka</p>
                                    </div>
                                </li> -->
                                <li>
                                    <div class="meta-icon"><i class="fa-solid fa-user-tie"></i></div>
                                    <div class="meta-txt">
                                        <p><span>Teaching</span> <br> <?php echo $tutor['languages_spoken']; ?></p>
                                    </div>
                                </li>
                                <li>
                                    <div class="meta-icon"><i class="fa-regular fa-flag"></i></div>
                                    <div class="meta-txt">
                                        <p><span>Teaching Languages</span> <br> <?php echo $tutor['native_language']; ?></p>
                                    </div>
                                </li>
                                <li>
                                    <div class="meta-icon"><i class="fa-regular fa-user"></i></div>
                                    <div class="meta-txt">
                                        <p><span>Working With</span> <br> <?php echo $tutor['working_with']; ?></p>
                                    </div>
                                </li>
                                <li>
                                    <div class="meta-icon"><i class="fa-solid fa-video"></i></div>
                                    <div class="meta-txt">
                                        <p><span>Platform</span> <br> Zoom, MS Teams</p>
                                    </div>
                                </li>
                                  <li>
                                  <div class="meta-icon"><i class="fa-solid fa-video"></i></div>
                                        <div class="meta-txt">
                                          <p>
                                            <span>Teaching Level</span> <br />
                                            <?php echo $tutor['levels_you_teach']; ?>
                                          </p>
                                        </div>
                                  </li>
                            </ul>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="col-lg-4">
                <div class="course-details-right">
                    <h5 class="pb-3">Book - <?php echo $tutor['username']; ?></h5>
                    <div class="inp-wrap small">
                    <?php if(!empty($_SESSION['user_id'])) { ?>
                        <?php if($_SESSION['role'] == 'Student'){ ?>
                            <input id="continue-button" class="site-link full" type="submit" value="Book Tutor" />
                        <?php }else if($_SESSION['role'] == 'Tutor'){ ?>
                            <a class="site-link full" href="/src/views/tutor/my-lessons">Become a Tutor</a>
                        <?php } ?>
                    <?php }else{ ?>
                        <input id="continue-button" class="site-link full" type="submit" value="Book Tutor" />
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
       </div>

       <div class="rating-sec">
          <div class="row">
            <div class="col-lg-3">
            <div class="rating-left">
                <div class="total-rating">
                    <h3><?php echo round($tutor['average_rating'], 1); ?></h3>
                    <div class="pt-2 pb-2">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <?php if($i <= $tutor['average_rating']): ?>
                        <i class="fa-solid fa-star"></i>
                        <?php else: ?>
                        <i class="fa-regular fa-star"></i>
                        <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <p><?php echo count($tutor['reviews']); ?> Reviews</p>
                </div>
            </div>
            </div>
            <div class="col-lg-9">
            <div class="rating-right">
                  <div class="tutor-review">
                      <?php foreach($tutor['reviews'] as $review): ?>
                      <div class="tutor-review-single">
                          <div class="tutor-review-txt">
                              <div class="d-flex align-items-center">
                                  <h5><?php echo htmlspecialchars($review['student_username']); ?></h5>
                                  <div class="ps-2">
                                      <?php for($i = 1; $i <= 5; $i++): ?>
                                      <?php if($i <= $review['star_rating']): ?>
                                      <i class="fa-solid fa-star"></i>
                                      <?php else: ?>
                                      <i class="fa-regular fa-star"></i>
                                      <?php endif; ?>
                                      <?php endfor; ?>
                                  </div>
                              </div>
                               <p class="pb-2"><strong><?php echo date("F j, Y", strtotime($review['review_date'])); ?></strong></p>
                              <p>
                                  <?php echo htmlspecialchars($review['review']); ?>
                              </p>
                          </div>
                      </div>
                      <?php endforeach; ?>
                  </div>
              </div>
            </div>
          </div>
       </div>

       
    </div>
  </section>


          <?php require_once 'footer.php'; ?>
  <script>
    $('#sandbox-container div').datepicker({
      todayHighlight: true,
    });
  </script>
  </body>
</html>
