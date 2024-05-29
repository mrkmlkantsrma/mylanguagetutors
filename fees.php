<?php
$page_title = 'MyLanguageTutor : Fees';
$page_description = '';

require_once 'header.php';
?>
  <section class="site-banner">
    <div class="site-banner-img"><img src="./assets/images/fees-banner.jpg" alt=""></div>
    <div class="inner-banner-txt">
      <div class="container">
        <div class="col-sm-6">
          <div class="banner-txt-main">
            <h2>Our Purpose</h2>
            <p>My Language Tutor is the reference for online tutoring for languages with a physical tutor. The platform is known as user-friendly and accessible to small budgets. Tutors join this company because they are paid more than the competition and while students have not paid a fortune either.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="fees-details">
    <div class="container">
       <div class="row justify-content-center">
       <?php if(!empty($_SESSION['user_id'])) { ?>
        <p class="log-plan"><small><em>Please Check our plans</em></small></p>
       <?php  }else{ ?>
        <p class="log-plan"><small><em>Please <a href="login">login to your dashboard</a> to purchase a plan</em></small></p>
       <?php } ?>
          <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="fees-structure var-1">
                <div class="price-icon"><i class="fa-solid fa-box"></i></div>
                <div>
                    <h3>Free</h3>
                    <h4>$0</h4>
                    <p>1st free trial class</p>
                    <p><small><em>The Free trial class is only available if you confirm one of these plans: 5 class plan, 10 class plan. 20 class plan.’</em></small></p>
                </div>
                <div class="price-btn">
                    
                </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="fees-structure var-2">
                <div class="price-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <div>
                    <h3>Premium</h3>
                    <h4>$30</h4>
                    <p>Annual access fee</p>
                </div>
                <div class="price-btn">
                    
                </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="fees-structure var-3">
                <div class="price-icon"><i class="fa-solid fa-layer-group"></i></div>
                <div>
                    <h3>Premium</h3>
                    <h4>$30</h4>
                    <p>30$ per hour/Less than 5 classes</p>
                </div>
                <div class="price-btn">
                    
                </div>
            </div>
          </div>
       </div> 
       <div class="row justify-content-center">
        <div class="col-sm-6 col-lg-4 col-xl-3">
          <div class="fees-structure var-4">
              <div class="price-icon"><i class="fa-solid fa-tree"></i></div>
              <div>
                  <h3>Premium</h3>
                  <h4>$140</h4>
                  <p>5 class plan</p>
              </div>
              <div class="price-btn">
                  
              </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl-3">
          <div class="fees-structure var-5">
              <div class="price-icon"><i class="fa-solid fa-landmark"></i></div>
              <div>
                  <h3>Premium</h3>
                  <h4>$270</h4>
                  <p>10 class plan</p>
              </div>
              <div class="price-btn">
                  
              </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl-3">
          <div class="fees-structure var-6">
              <div class="price-icon"><i class="fa-solid fa-store"></i></div>
              <div>
                  <h3>Premium</h3>
                  <h4>$500</h4>
                  <p>20 class plan</p>
              </div>
              <div class="price-btn">
                  
              </div>
          </div>
        </div>
       </div>
    </div>
  </section>

  <!-- <section class="custom-plan">
    <div class="container">
        <div class="text-center">
            <a class="site-link" href="contacts">Request  A Custom Plan</a>
            <a class="site-link green" href="contacts">Request  A Group Plan</a>
        </div>
    </div>
  </section> -->

  <section class="benefits">
    <div class="container">
        <h2 class="text-center">Our Services and your benefits</h2>
        <div class="row justify-content-center">
            <div class="col-sm-6 col-lg-4 col-xl-4">
                <div class="fees-structure alt">
                    <div class="price-icon"><i class="fa-solid fa-box"></i></div>
                    <h4>Features of my services</h4>
                    <p>Our services are known to be flexible and affordable. The platform is user-friendly and has additional learning resources.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4 col-xl-4">
                <div class="fees-structure alt">
                    <div class="price-icon"><i class="fa-solid fa-box"></i></div>
                    <h4>Customer benefits</h4>
                    <p>Our language learning methods are based on communication and connection. Students learn a new language quickly and develop lasting relationships with tutors.</p>
                </div>
            </div>
        </div>
    </div>
  </section>

  <?php require_once 'footer.php'; ?>
  </body>
</html>
