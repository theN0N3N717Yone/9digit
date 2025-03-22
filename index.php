<?php
require_once('system/connectivity_functions.php');
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-wide " dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="front-pages">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?= getPortalInfo('webName') ?> | Home</title>

    
    <meta name="description" content="Most Powerful &amp; Comprehensive Bootstrap 5 HTML Admin Dashboard Template built for developers!" />
    <meta name="keywords" content="dashboard, bootstrap 5 dashboard, bootstrap 5 design, bootstrap 5">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

    
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />
    

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
    <link rel="stylesheet" href="assets/vendor/css/pages/front-page.css" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/css/rtl/kavya-all.css" />
    <link rel="stylesheet" href="assets/vendor/libs/nouislider/nouislider.css" />
<link rel="stylesheet" href="assets/vendor/libs/swiper/swiper.css" />

    <!-- Page CSS -->
    
<link rel="stylesheet" href="assets/vendor/css/pages/front-page-landing.css" />

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/js/front-config.js"></script>
    <style>
        .kavya-tile {
    background: linear-gradient(to right, #ff0000 0%, #1c008b 47.92%, #fb0003 100%);
    background-size: 200% auto;
    color: #566a7f;
    background-clip: text;
    line-height: 1.2;
    text-fill-color: rgba(0, 0, 0, 0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: rgba(0, 0, 0, 0);
    animation: shine 2s ease-in-out infinite alternate;
}
    </style>
</head>

<body>

<script src="assets/vendor/js/dropdown-hover.js"></script>
<script src="assets/vendor/js/mega-dropdown.js"></script>

<!-- Navbar: Start -->
<nav class="layout-navbar shadow-none py-0">
  <div class="container">
    <div class="navbar navbar-expand-lg landing-navbar px-3 px-md-4 ">
      <!-- Menu logo wrapper: Start -->
      <div class="navbar-brand app-brand demo d-flex py-0 me-4">
        <!-- Mobile menu toggle: Start-->
        <!-- Mobile menu toggle: End-->
        <a href="" class="app-brand-link">
          <span class="fw-bold kavya-tile" style="font-size: 30px;"><?= getPortalInfo('webName') ?></span>

        </a>
      </div>
      <!-- Menu logo wrapper: End -->
      <!-- Menu wrapper: Start -->
      <div class="collapse navbar-collapse landing-nav-menu" id="navbarSupportedContent">
        <button class="navbar-toggler border-0 text-heading position-absolute end-0 top-0 scaleX-n1-rtl" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="tf-icons bx bx-x bx-sm"></i>
        </button>
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link fw-medium" aria-current="page" href="">Home</a>
          </li>
        </ul>
      </div>
      <div class="landing-menu-overlay d-lg-none"></div>
      <!-- Menu wrapper: End -->
      <!-- Toolbar: Start -->
      <ul class="navbar-nav flex-row align-items-center ms-auto">
        <!-- navbar button: Start -->
        <li>
          <a href="auth-login.php" class="btn btn-primary" target="_blank"><span class="tf-icons bx bx-user me-md-1"></span><span class="d-none d-md-block">Login/Register</span></a>
        </li>
        <!-- navbar button: End -->
      </ul>
      <!-- Toolbar: End -->
    </div>
  </div>
</nav>
<!-- Navbar: End -->


<!-- Sections:Start -->


<div data-bs-spy="scroll" class="scrollspy-example">
  <section id="hero-animation">
    <div id="landingHero" class="section-py landing-hero position-relative">
      <div class="container">
        <div class="hero-text-box text-center">
          <h1 class="text-primary hero-title display-4 fw-bold">Get Your Official Documents Printed with Ease</h1>
          <h2 class="hero-sub-title h6 mb-4 pb-1">
            Your one-stop solution for printing PAN cards and government IDs<br class="d-none d-lg-block" />
            with a reliable and customizable service.
          </h2>
        </div>
      </div>
    </div>
  </section>
  <!-- Hero: End -->
  
    <!-- Secure Document Printing Services: Start -->
    <section id="secureDocumentPrinting" class="section-py secure-document-printing">
      <div class="container">
        <div class="text-center mb-3 pb-1">
          <span class="badge bg-label-primary">Secure Document Printing Services</span>
        </div>
        <h3 class="text-center mb-1">Print Your Essential Documents with Confidence</h3>
        <p class="text-center mb-3 mb-md-5 pb-3">
          Discover our reliable and secure printing services for a range of essential government IDs and official documents.
        </p>
        <div class="features-icon-wrapper row gx-0 gy-4 g-sm-5">
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="text-center mb-3">
              <img src="assets/img/front-pages/icons/aadhaar.svg" alt="Aadhaar card" width="100" />
            </div>
            <h5 class="mb-3">Aadhaar Card Printing</h5>
            <p class="features-icon-description">
              Experience top-notch printing for your Aadhaar cards with a focus on security and precision.
            </p>
          </div>
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="text-center mb-3">
              <img src="assets/img/front-pages/icons/voter.webp" alt="Voter ID card" width="100" />
            </div>
            <h5 class="mb-3">Voter ID Printing</h5>
            <p class="features-icon-description">
              Reliable printing services for Voter ID cards, ensuring accuracy and confidentiality.
            </p>
          </div>
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="text-center mb-3">
              <img src="assets/img/front-pages/icons/ayushman.png" alt="Ayushman card" width="100" />
            </div>
            <h5 class="mb-3">Ayushman Card Printing</h5>
            <p class="features-icon-description">
              Trust us for Ayushman Bharat health card printing, emphasizing security and clarity.
            </p>
          </div>
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="text-center mb-3">
              <img src="assets/img/front-pages/icons/rashan.jfif" alt="Ration card" width="100" />
            </div>
            <h5 class="mb-3">Ration Card Printing</h5>
            <p class="features-icon-description">
              Secure and efficient printing for your Ration cards, meeting government standards.
            </p>
          </div>
          <div class="col-lg-4 col-sm-6 text-center features-icon-box">
            <div class="text-center mb-3">
              <img src="assets/img/front-pages/icons/check-pan-card-details.jpg" alt="PAN card" width="100" />
            </div>
            <h5 class="mb-3">PAN Card Printing</h5>
            <p class="features-icon-description">
              Print your PAN cards with us, ensuring data privacy and official compliance.
            </p>
          </div>
          <!-- Add more government ID printing services as needed -->
        </div>
      </div>
    </section>
    <!-- Secure Document Printing Services: End -->
  

  <!-- Real customers reviews: Start -->
  <section id="landingReviews" class="section-py bg-body landing-pricing">
    <!-- What people say slider: Start -->
    <div class="container">
      <div class="row align-items-center gx-0 gy-4 g-lg-5">
        <div class="col-md-6 col-lg-5 col-xl-3">
          <div class="mb-3 pb-1">
            <span class="badge bg-label-primary">Our Services</span>
          </div>
          <h3 class="mb-1">What we offer</h3>
          <p class="mb-3 mb-md-5">
            Explore the range of services we provide <br class="d-none d-xl-block" />
            to meet your needs.
          </p>
          <div class="landing-reviews-btns d-flex align-items-center gap-3">
            <button id="reviews-previous-btn" class="btn btn-label-primary reviews-btn" type="button">
              <i class="bx bx-chevron-left bx-sm"></i>
            </button>
            <button id="reviews-next-btn" class="btn btn-label-primary reviews-btn" type="button">
              <i class="bx bx-chevron-right bx-sm"></i>
            </button>
          </div>
        </div>
        <div class="col-md-6 col-lg-7 col-xl-9">
          <div class="swiper-reviews-carousel overflow-hidden mb-5 pb-md-2 pb-md-3">
            <div class="swiper" id="swiper-reviews">
              <div class="swiper-wrapper">
                <div class="swiper-slide">
                  <div class="card h-100">
                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                      <div class="mb-3">
                        <img src="assets/img/front-pages/icons/aadhaar.svg" alt="Aadhaar Print Service" class="service-img img-fluid" width="50"/>
                      </div>
                      <h5>
                        Aadhaar Print Service
                      </h5>
                      <p>
                        Get your Aadhaar card printed hassle-free with our Aadhaar Print Service. Quick and reliable printing for your convenience.
                      </p>
                      <div class="text-warning mb-3">
                        <!-- Add star ratings if applicable for the service -->
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="card h-100">
                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                      <div class="mb-3">
                        <img src="assets/img/front-pages/icons/voter.webp" alt="Voter Print Service" class="service-img img-fluid" width="50" />
                      </div>
                      <h5>
                        Voter Print Service
                      </h5>
                      <p>
                        Ensure your participation in democracy by availing our Voter Print Service. Quick and secure printing of your voter ID for a seamless voting experience.
                      </p>
                      <div class="text-warning mb-3">
                        <!-- Add star ratings if applicable for the service -->
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="swiper-slide">
                  <div class="card h-100">
                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                      <div class="mb-3">
                        <img src="assets/img/front-pages/icons/ayushman.png" alt="Ayushman Print Service" class="service-img img-fluid" width="50" />
                      </div>
                      <h5>
                        Ayushman Print Service
                      </h5>
                      <p>
                        Take advantage of our Ayushman Print Service for hassle-free printing of your Ayushman Bharat health card. Ensure quick access to healthcare services with our secure printing solutions.
                      </p>
                      <div class="text-warning mb-3">
                        <!-- Add star ratings if applicable for the service -->
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="swiper-slide">
                  <div class="card h-100">
                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                      <div class="mb-3">
                        <img src="assets/img/front-pages/icons/rashan.jfif" alt="Rashan Card Print Service" class="service-img img-fluid" width="50" />
                      </div>
                      <h5>
                        Rashan Card Print Service
                      </h5>
                      <p>
                        Avail our Rashan Card Print Service for swift and secure printing of your ration card. Ensure easy access to essential food supplies with our reliable printing solutions.
                      </p>
                      <div class="text-warning mb-3">
                        <!-- Add star ratings if applicable for the service -->
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="swiper-slide">
                  <div class="card h-100">
                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                      <div class="mb-3">
                        <img src="assets/img/front-pages/icons/license-icon.webp" alt="Driving License Print Service" class="service-img img-fluid" width="50" />
                      </div>
                      <h5>
                        Driving License Print Service
                      </h5>
                      <p>
                        Obtain your driving license hassle-free with our Driving License Print Service. Swift and secure printing to ensure you hit the road with confidence.
                      </p>
                      <div class="text-warning mb-3">
                        <!-- Add star ratings if applicable for the service -->
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="swiper-slide">
                  <div class="card h-100">
                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                      <div class="mb-3">
                        <img src="assets/img/front-pages/icons/check-pan-card-details.jpg" alt="Aadhaar to PAN Find Service" class="service-img img-fluid" width="50" />
                      </div>
                      <h5>
                        Aadhaar to PAN Find Service
                      </h5>
                      <p>
                        Seamlessly link your Aadhaar with PAN using our Aadhaar to PAN Find Service. Swift and accurate identification to ensure compliance with official regulations.
                      </p>
                      <div class="text-warning mb-3">
                        <!-- Add star ratings if applicable for the service -->
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="swiper-slide">
                  <div class="card h-100">
                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                      <div class="mb-3">
                        <img src="assets/img/icons/voter.webp" alt="Voter Mobile Linking Service" class="service-img img-fluid" width="50" />
                      </div>
                      <h5>
                        Voter Mobile Linking Service
                      </h5>
                      <p>
                        Simplify your voting experience with our Voter Mobile Linking Service. Link your mobile number to your voter ID for easy access to election-related information and updates.
                      </p>
                      <div class="text-warning mb-3">
                        <!-- Add star ratings if applicable for the service -->
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                        <i class="bx bxs-star bx-sm"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="swiper-button-next"></div>
              <div class="swiper-button-prev"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Fun facts: Start -->
  <section id="landingFunFacts" class="section-py landing-fun-facts">
    <div class="container">
      <div class="row gy-3">
        <div class="col-sm-6 col-lg-3">
          <div class="card border border-label-primary shadow-none">
            <div class="card-body text-center">
              <img src="assets/img/front-pages/icons/laptop.png" alt="laptop" class="mb-2" />
              <h5 class="h2 mb-1">7.1k+</h5>
              <p class="fw-medium mb-0">
                Support Tickets<br />
                Resolved
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card border border-label-success shadow-none">
            <div class="card-body text-center">
              <img src="assets/img/front-pages/icons/user-success.png" alt="laptop" class="mb-2" />
              <h5 class="h2 mb-1">50k+</h5>
              <p class="fw-medium mb-0">
                Join creatives<br />
                community
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card border border-label-info shadow-none">
            <div class="card-body text-center">
              <img src="assets/img/front-pages/icons/diamond-info.png" alt="laptop" class="mb-2" />
              <h5 class="h2 mb-1">4.8/5</h5>
              <p class="fw-medium mb-0">
                Highly Rated<br />
                Products
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card border border-label-warning shadow-none">
            <div class="card-body text-center">
              <img src="assets/img/front-pages/icons/check-warning.png" alt="laptop" class="mb-2" />
              <h5 class="h2 mb-1">100%</h5>
              <p class="fw-medium mb-0">
                Money Back<br />
                Guarantee
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Fun facts: End -->

</div>

<!-- / Sections:End -->



<!-- Footer: Start -->
<footer class="landing-footer bg-body footer-text">
  <div class="footer-top position-relative overflow-hidden z-1">
    <img class="footer-bg banner-bg-img z-n1" />
    <div class="container">
      <div class="row gx-0 gy-4 g-md-5">
        <div class="col-lg-5">
          <a href="" class="app-brand-link mb-4">
            <span class="footer-link fw-bold" style="font-size: 32px"><?= getPortalInfo('webName') ?></span>
          </a>
          <p class="footer-text footer-logo-description mb-4">
            Most developer friendly & highly customisable Admin Dashboard Template.
          </p>
          <form class="footer-form">
            <label for="footer-email" class="small">Subscribe to newsletter</label>
            <div class="d-flex mt-1">
              <input type="email" class="form-control rounded-0 rounded-start-bottom rounded-start-top" id="footer-email" placeholder="Your email" />
              <button type="submit" class="btn btn-primary shadow-none rounded-0 rounded-end-bottom rounded-end-top">
                Subscribe
              </button>
            </div>
          </form>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <h6 class="footer-title mb-4">Services</h6>
          <ul class="list-unstyled">
            <li class="mb-3">
              <a class="footer-link">Aadhaar Print</a>
            </li>
            <li class="mb-3">
              <a class="footer-link">Voter Print</a>
            </li>
            <li class="mb-3">
              <a class="footer-link">Rashan Print</a>
            </li>
            <li class="mb-3">
              <a class="footer-link">Ayushman Print</a>
            </li>
            <li class="mb-3">
              <a class="footer-link">Licence Print</a>
            </li>
          </ul>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <h6 class="footer-title mb-4">Or Portal</h6>
          <ul class="list-unstyled">
            <li class="mb-3">
              <a href="https://sms.digiserver.in" target="_blanck" class="footer-link">Whatsapp API</a>
            </li>
            <li class="mb-3">
              <a href="https://api.sprintpan.in" target="_blanck" class="footer-link">Verification API</a>
            </li>
            <li class="mb-3">
              <a href="https://pay.pansprint.in" target="_blanck" class="footer-link">Payment Getway</a>
            </li>
            <li class="mb-3">
              <a href="https://pansprint.in" target="_blanck" class="footer-link">Instant Pan</a>
            </li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-4">
          <h6 class="footer-title mb-4">Download our app</h6>
          <a href="javascript:void(0);" class="d-block footer-link mb-3 pb-2"><img src="assets/img/front-pages/landing-page/apple-icon.png" alt="apple icon" /></a>
          <a href="javascript:void(0);" class="d-block footer-link"><img src="assets/img/front-pages/landing-page/google-play-icon.png" alt="google play icon" /></a>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom py-3">
    <div class="container d-flex flex-wrap justify-content-between flex-md-row flex-column text-center text-md-start">
      <div class="mb-2 mb-md-0">
        <span class="footer-text">©
          <script>
            document.write(new Date().getFullYear());

          </script>
        </span>
        <a href="#" target="_blank" class="fw-medium text-white footer-link" style=""><?= getPortalInfo('webName') ?>,</a>
        <span class="footer-text"> Made with ❤️</span>
      </div>
      <div>
        <a href="#" class="footer-link me-3" target="_blank">
          <img src="assets/img/front-pages/icons/github-light.png" alt="github icon" data-app-light-img="front-pages/icons/github-light.png" data-app-dark-img="front-pages/icons/github-dark.png" />
        </a>
        <a href="#" class="footer-link me-3" target="_blank">
          <img src="assets/img/front-pages/icons/facebook-light.png" alt="facebook icon" data-app-light-img="front-pages/icons/facebook-light.png" data-app-dark-img="front-pages/icons/facebook-dark.png" />
        </a>
        <a href="#" class="footer-link me-3" target="_blank">
          <img src="assets/img/front-pages/icons/twitter-light.png" alt="twitter icon" data-app-light-img="front-pages/icons/twitter-light.png" data-app-dark-img="front-pages/icons/twitter-dark.png" />
        </a>
        <a href="#" class="footer-link" target="_blank">
          <img src="assets/img/front-pages/icons/instagram-light.png" alt="google icon" data-app-light-img="front-pages/icons/instagram-light.png" data-app-dark-img="front-pages/icons/instagram-dark.png" />
        </a>
      </div>
    </div>
  </div>
</footer>
<!-- Footer: End -->


  
  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->
  <script src="assets/vendor/libs/popper/popper.js"></script>
  <script src="assets/vendor/js/bootstrap.js"></script>
  
  <!-- endbuild -->

  <!-- Vendors JS -->
  <script src="assets/vendor/libs/nouislider/nouislider.js"></script>
<script src="assets/vendor/libs/swiper/swiper.js"></script>

  <!-- Main JS -->
  <script src="assets/js/front-main.js"></script>
  

  <!-- Page JS -->
  <script src="assets/js/front-page-landing.js"></script>
  
</body>

</html>

<!-- beautify ignore:end -->