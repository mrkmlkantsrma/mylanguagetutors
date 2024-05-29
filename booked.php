<?php
$page_title = 'MyLanguageTutor : Booked';
$page_description = '';

require_once 'header.php';
?>
  <section class="booked">
    <div class="container">
        <div class="booked-in">
            <div class="booked-left" style="display: none;">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                      <a class="nav-link active" data-bs-toggle="tab" href="#tab-1">
                        <span class="tab-icon"><i class="fa-solid fa-server"></i></span>
                        Service
                      </a>
                    </li>
                    <!-- <li class="nav-item">
                      <a class="nav-link" data-bs-toggle="tab" href="#tab-2">
                        <span class="tab-icon"><i class="fa-solid fa-calendar-days"></i></span>
                        Date & Time
                      </a>
                    </li> -->
                    <!-- <li class="nav-item">
                      <a class="nav-link" data-bs-toggle="tab" href="#tab-3">
                        <span class="tab-icon"><i class="fa-solid fa-file-lines"></i></span>
                        Basic Details
                      </a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-4">
                            <span class="tab-icon"><i class="fa-solid fa-calendar-check"></i></span>
                            Summary
                        </a>
                    </li>
                </ul>
            </div>
            <div class="booked-right">
                <div class="tab-content">
                    <div class="tab-pane container active" id="tab-1">
                        <h5>Selected Plan</h5>
                        <div class="service-select">
                            <ul>
                                <li class="selected">
                                    <div class="service-single">
                                        <div class="service-icon"><i class="fa-solid fa-box"></i></div>
                                        <div class="service-txt">
                                            <h6>1st free trial class</h6>
                                            <p>Duration: <strong>1 h</strong></p>
                                            <div class="select-duration mt-2">
                                              <label for="">Select Duration</label>
                                              <select name="" id="">
                                                <option value="">1hr</option>
                                                <option value="">2hr</option>
                                                <option value="">3hr</option>
                                                <option value="">4hr</option>
                                              </select>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <!-- <li>
                                    <div class="service-single">
                                        <div class="service-icon"><i class="fa-solid fa-shield-halved"></i></div>
                                        <div class="service-txt">
                                            <h6>Annual access fee</h6>
                                            <p>Duration: <strong>1 year</strong></p>
                                            <div class="select-duration">
                                              <label for="">Select Duration</label>
                                              <select name="" id="">
                                                <option value="">1 year</option>
                                                <option value="">2 year</option>
                                                <option value="">3 year</option>
                                                <option value="">4 year</option>
                                              </select>
                                            </div>
                                            <span class="price-tag">$30.00</span>
                                        </div>
                                    </div>
                                </li> -->
                                <!-- <li>
                                    <div class="service-single">
                                        <div class="service-icon"><i class="fa-solid fa-layer-group"></i></div>
                                        <div class="service-txt">
                                            <h6>Less than 5 classes</h6>
                                            <p>Duration: <strong>1 h</strong></p>
                                            <div class="select-duration">
                                              <label for="">Select Duration</label>
                                              <select name="" id="">
                                                <option value="">1hr</option>
                                                <option value="">2hr</option>
                                                <option value="">3hr</option>
                                                <option value="">4hr</option>
                                              </select>
                                            </div>
                                            <span class="price-tag">$30.00</span>
                                        </div>
                                    </div>
                                </li> -->
                                <!-- <li>
                                    <div class="service-single">
                                        <div class="service-icon"><i class="fa-solid fa-tree"></i></div>
                                        <div class="service-txt">
                                            <h6>5 class plan with Annual access</h6>
                                            <p>Duration: <strong>1 h</strong></p>
                                            <div class="select-duration">
                                              <label for="">Select Duration</label>
                                              <select name="" id="">
                                                <option value="">1hr</option>
                                                <option value="">2hr</option>
                                                <option value="">3hr</option>
                                                <option value="">4hr</option>
                                              </select>
                                            </div>
                                            <span class="price-tag">$140.00</span>
                                        </div>
                                    </div>
                                </li> -->
                                <!-- <li>
                                    <div class="service-single">
                                        <div class="service-icon"><i class="fa-solid fa-landmark"></i></div>
                                        <div class="service-txt">
                                            <h6>10 class plan with Annual access</h6>
                                            <p>Duration: <strong>10 h</strong></p>
                                            <div class="select-duration">
                                              <label for="">Select Duration</label>
                                              <select name="" id="">
                                                <option value="">10 hr</option>
                                                <option value="">20 hr</option>
                                                <option value="">30 hr</option>
                                                <option value="">40 hr</option>
                                              </select>
                                            </div>
                                            <span class="price-tag">$270.00</span>
                                        </div>
                                    </div>
                                </li> -->
                                <!-- <li>
                                    <div class="service-single">
                                        <div class="service-icon"><i class="fa-solid fa-store"></i></div>
                                        <div class="service-txt">
                                            <h6>20 class plan with Annual access</h6>
                                            <p>Duration: <strong>20 h</strong></p>
                                            <div class="select-duration">
                                              <label for="">Select Duration</label>
                                              <select name="" id="">
                                                <option value="">20 hr</option>
                                                <option value="">30 hr</option>
                                                <option value="">40 hr</option>
                                                <option value="">50 hr</option>
                                              </select>
                                            </div>
                                            <span class="price-tag">$500.00</span>
                                        </div>
                                    </div>
                                </li> -->
                            </ul>
                        </div>

                        <div class="tab-nxt">
                            <a class="site-link btnNext">Next</a>
                        </div>
                    </div>

                    <div class="tab-pane container fade" id="tab-3">
                        <h5>Basic Details</h5>
                        <div class="inp-wrap small">
                          <label for="">Firstname</label>
                          <input class="inp" type="text" placeholder="Enter your firstname">
                        </div>
                        <div class="inp-wrap small">
                          <label for="">Lastname</label>
                          <input class="inp" type="text" placeholder="Enter your Lastname">
                        </div>
                        <div class="inp-wrap small">
                          <label for="">Email</label>
                          <input class="inp" type="email" placeholder="Enter your Email">
                        </div>
                        <div class="inp-wrap small">
                          <label for="">Phone</label>
                          <input class="inp" type="text" placeholder="(506) 234-5678">
                        </div>
                        <div class="inp-wrap small">
                          <label for="">Note</label>
                          <textarea class="inp" name="" id="" placeholder="Enter note details"></textarea>
                        </div>
                        <div class="tab-nxt">
                            <a class="site-link green btnPrevious">Back</a>
                            <a class="site-link btnNext">Next</a>
                          </div>
                      </div>

                    <div class="tab-pane container fade" id="tab-4">
                      <h5>Summary</h5>
                      <p class="pt-3">Your appointment booking summary</p>
                      <ul class="summary-list">
                        <li><span>Customer:</span><span>mylanguagetutor</span></li>
                        <li><span>Service:</span><span>Annual access fee</span></li>
                        <li><span>Date & Time:</span><span>July 4, 2023, 09:00 am - 09:01 am</span></li>
                        <li class="total"><span>Total Amount Payable:</span><span>$30.00</span></li>
                      </ul>
                      <h5 class="pt-5">Select Payment Method</h5>
                      <ul class="payment-method">
                        <ul>
                          <li>
                            <div class="payment-method-single">
                              <img src="./assets/images/paypal.png" alt="">
                              <span>PayPal</span>
                            </div>
                          </li>
                        </ul>
                      </ul>
                      <div class="tab-nxt">
                        <a class="site-link green btnPrevious">Back</a>
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
