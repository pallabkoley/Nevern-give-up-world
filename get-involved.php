<?php
// get-involved.php - Get Involved Page

// Define page-specific CSS
$extraCSS = [
    'assets/css/get-involved.css'
];

include 'includes/header.php';
?>

<!-- GET INVOLVED HERO SECTION -->
<section class="get-involved-hero">
    <picture>
        <source media="(max-width: 768px)" srcset="assets/images/get-involved-banner-mobile.png">
        <img src="assets/images/get-involved-banner.png" alt="Get Involved">
    </picture>
</section>

<!-- INVOLVEMENT OPTIONS SECTION -->
<section class="involvement-options-section">
    <div class="container">
        <div class="involvement-grid">
            
            <!-- Volunteer -->
            <a href="volunteer.php" class="involvement-card">
                <div class="involvement-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h3>Volunteer</h3>
                <p>Share your time and skills</p>
            </a>

            <!-- Partner With Us -->
            <a href="partner-with-us.php" class="involvement-card">
                <div class="involvement-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>Partner With Us</h3>
                <p>Work together for greater impact</p>
            </a>

            <!-- CSR Partnership -->
            <a href="csr-partnership.php" class="involvement-card">
                <div class="involvement-icon">
                    <i class="fas fa-smile"></i>
                </div>
                <h3>CSR Partnership</h3>
                <p>Support our programs through CSR</p>
            </a>

            <!-- Support a Program -->
            <a href="support.php" class="involvement-card">
                <div class="involvement-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3>Support a Program</h3>
                <p>Help us reach more people</p>
            </a>

        </div>
    </div>
</section>

<!-- IMPACT CTA SECTION -->
<section class="impact-cta-section">
    <picture>
        <source media="(max-width: 768px)" srcset="assets/images/get-involved-banner2-mobile.png">
        <img src="assets/images/get-involved-banner2.png" alt="Together We Can Create Greater Impact">
    </picture>
    <div class="impact-cta-overlay">
        <div class="container">
            <h2>Together, We Can Create<br>Greater Impact</h2>
            <a href="partner.php" class="btn btn-primary btn-lg">Get Involved Today <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>