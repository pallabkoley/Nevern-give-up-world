<?php
// partner-with-us.php - Partner With Us Page

// Define page-specific CSS
$extraCSS = [
    'assets/css/partner-with-us.css'
];

include 'includes/header.php';
?>


<!-- PARTNER HERO SECTION -->
<section class="partner-hero">
    <picture>
        <source media="(max-width: 768px)" srcset="assets/images/partner-with-us-banner-mobile.jpg">
        <img src="assets/images/partner-with-us-banner.png" alt="Partner With Us">
    </picture>
</section>

<!-- PARTNERSHIP TYPES SECTION -->
<section class="partnership-types-section">
    <div class="container">
        <div class="partnership-grid">
            
            <!-- Corporates -->
            <div class="partnership-card">
                <div class="partnership-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3>Corporates</h3>
                <p>Support skill development, education and community programs through CSR initiatives.</p>
            </div>

            <!-- Small Businesses -->
            <div class="partnership-card">
                <div class="partnership-icon">
                    <i class="fas fa-store"></i>
                </div>
                <h3>Small Businesses</h3>
                <p>Partner with us for employee capacity building and soft-skill programs.</p>
            </div>

            <!-- Educational Institutions -->
            <div class="partnership-card">
                <div class="partnership-icon">
                    <i class="fas fa-university"></i>
                </div>
                <h3>Educational Institutions</h3>
                <p>Collaborate on training, education and youth-development initiatives.</p>
            </div>

            <!-- Industry Partners -->
            <div class="partnership-card">
                <div class="partnership-icon">
                    <i class="fas fa-industry"></i>
                </div>
                <h3>Industry Partners</h3>
                <p>Support logistics training through industry exposure, mentoring, internships and employment opportunities.</p>
            </div>

            <!-- Volunteers -->
            <div class="partnership-card">
                <div class="partnership-icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <h3>Volunteers</h3>
                <p>Share your time, knowledge and expertise with communities.</p>
            </div>

            <!-- Individual Supporters -->
            <div class="partnership-card">
                <div class="partnership-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h3>Individual Supporters</h3>
                <p>Help us expand education, skills and well-being programs.</p>
            </div>

        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="partner-cta-section">
    <div class="container">
        <h2>Ready to Make a Difference?</h2>
        <div class="partner-cta-buttons">
            <a href="partner" class="btn btn-primary btn-lg">Become a Partner <i class="fas fa-arrow-right"></i></a>
            <a href="volunteer" class="btn btn-outline btn-lg">Become a Volunteer <i class="fas fa-arrow-right"></i></a>
            <a href="support" class="btn btn-outline btn-lg">Support Our Programs <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>