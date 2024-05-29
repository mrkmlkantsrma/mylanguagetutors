<footer class="site-footer">
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        <div class="col-6 col-lg-3">
                            <div class="footer-single">
                                <a href="/"><img src="./assets/images/footer-logo.png" alt="MyLanguageTutor Logo" /></a>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="footer-single">
                                <h5>Quick Links</h5>
                                <ul class="footer-link">
                                    <li><a href="/">About Us</a></li>
                                    <li><a href="find-tutor">Find a Tutor</a></li>
                                    <li><a href="fees">Fees</a></li>
                                    <li><a href="resources">Resources</a></li>
                                    <li><a href="group-classes">Group Classes</a></li>
                                    <li><a href="contacts">Contact</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="footer-single">
                                <h5>Get in Touch</h5>
                                <address>
                                    <ul class="footer-link">
                                        <li><a href="tel:+15145471551">+1 819-635-5969</a></li>
                                        <li><a href="#">Gatineau, Québec, Canada</a></li></li>
                                        <li><a href="mailto:contact@mylanguagetutor.ca">contact@mylanguagetutor.ca</a></li>
                                        <li><a href="#">My Language Tutor is registered in the province of Québec under this legal name: 9488-5381 Québec inc.</a></li>
                                    </ul>
                                </address>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="footer-single">
                                <h5>Connect With Us</h5>
                                <ul class="foot-social">
                                    <li><a href="https://www.facebook.com/profile.php?id=100089887141753" aria-label="Visit our Facebook Page"><i class="fa-brands fa-square-facebook"></i></a></li>
                                    <li><a href="https://www.instagram.com/mylanguagetutor01" target="_blank" rel="noopener"><i class="fa-brands fa-square-instagram"></i></a></li>
                                    <li><a href="https://www.linkedin.com/company/my-language-tutor/?viewAsMember=true" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="footer-bottom text-center">
                    <p>© 2024 MyLanguageTutor. All Rights Reserved.</p>
                </div>
            </div>
        </footer>

        <div class="lang-select">
            <div class="lang-close"><i class="fa-solid fa-circle-xmark"></i></div>
            <div class="container">
                <ul>
                    <li data-lang="en"><a href="" onclick="triggerTranslation('en')"> English</a></li>
                    <li data-lang="fr"><a href="" onclick="triggerTranslation('fr')"> French</a></li>
                    <li data-lang="es"><a href="" onclick="triggerTranslation('es')"> Spanish</a></li>
                    <li data-lang="ar"><a href="" onclick="triggerTranslation('ar')"> Arabic</a></li>
                    <li data-lang="hi"><a href="" onclick="triggerTranslation('hi')"> Hindi</a></li>
                    <li data-lang="bn"><a href="" onclick="triggerTranslation('bn')"> Bengali</a></li>
                    <li data-lang="pt"><a href="" onclick="triggerTranslation('pt')"> Portuguese</a></li>
                    <li data-lang="ru"><a href="" onclick="triggerTranslation('ru')"> Russian</a></li>
                    <li data-lang="ja"><a href="" onclick="triggerTranslation('ja')"> Japanese</a></li>
                    <li data-lang="ko"><a href="" onclick="triggerTranslation('ko')"> Korean</a></li>
                    <li data-lang="af"><a href="" onclick="triggerTranslation('af')"> Afrikaans</a></li>
                    <li data-lang="sq"><a href="" onclick="triggerTranslation('sq')"> Albanian</a></li>
                    <li data-lang="am"><a href="" onclick="triggerTranslation('am')"> Amharic</a></li>
                    <li data-lang="hy"><a href="" onclick="triggerTranslation('hy')"> Armenian</a></li>
                    <li data-lang="az"><a href="" onclick="triggerTranslation('az')"> Azerbaijani</a></li>
                </ul>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://unpkg.com/tilt.js@1.2.1/dest/tilt.jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
        <script src="./assets/js/custom.js"></script>
        <script src="assets/js/continue.js"></script> 
        <script src="./assets/js/translate.js"></script>
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", (event) => {
                let avlLanguageLinks = document.querySelectorAll(".avl-language .site-link");
                let currentUrl = new URL(window.location.href);
                let currentLanguage = currentUrl.searchParams.get("language");

                avlLanguageLinks.forEach((link) => {
                    let url = new URL(link.href);
                    let language = url.searchParams.get("language");

                    if (language === currentLanguage) {
                        link.classList.add("green");
                    } else {
                        link.classList.remove("green");
                    }
                });
            });
        </script>