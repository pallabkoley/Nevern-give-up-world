<?php
// child-education.php

// Define page-specific CSS
$extraCSS = [
    'assets/css/child-education.css'
];

include 'includes/header.php';
?>


<!-- PROGRAM HERO SECTION -->
<section class="program-detail-hero child-hero">
    <picture>
        <source media="(max-width: 768px)" srcset="assets/images/child-education-banner-mobile.jpg">
        <img src="assets/images/child-education-banner.png" alt="Child Education Program">
    </picture>
</section>

<!-- FOCUS & APPROACH SECTION -->
<section class="focus-approach-section">
    <div class="container focus-approach-grid">
        
        <!-- Left: Our Focus -->
        <div class="focus-box">
            <h3>Our Focus</h3>
            <ul class="focus-list">
                <li>
                    <span class="focus-list-icon green-icon"><i class="fas fa-book-open"></i></span>
                    <div class="focus-list-text">
                        <strong>Foundational Learning</strong>
                        <span>(Reading, writing, basic math, general knowledge)</span>
                    </div>
                </li>
                <li>
                    <span class="focus-list-icon blue-icon"><i class="fas fa-lightbulb"></i></span>
                    <div class="focus-list-text">
                        <strong>Learning Support</strong>
                        <span>(Academic assistance, materials, mentorship)</span>
                    </div>
                </li>
                <li>
                    <span class="focus-list-icon green-icon"><i class="fas fa-child"></i></span>
                    <div class="focus-list-text">
                        <strong>Child Development</strong>
                        <span>(Communication, confidence, creativity, life skills)</span>
                    </div>
                </li>
                <li>
                    <span class="focus-list-icon blue-icon"><i class="fas fa-graduation-cap"></i></span>
                    <div class="focus-list-text">
                        <strong>Education Awareness</strong>
                        <span>(Parent engagement, regular schooling, school participation)</span>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Right: Our Approach -->
        <div class="approach-box">
            <div class="approach-header">
                <div class="approach-icon"><i class="fas fa-bullseye"></i></div>
                <h3>Our Approach</h3>
            </div>
            
            <div class="approach-steps">
                <div class="approach-step">
                    <span class="step-icon"><i class="fas fa-search"></i></span>
                    <span>Identify</span>
                    <i class="fas fa-arrow-right step-arrow"></i>
                    <span>Support</span>
                    <i class="fas fa-arrow-right step-arrow"></i>
                </div>
                <div class="approach-step">
                    <span class="step-icon"><i class="fas fa-chalkboard-teacher"></i></span>
                    <span>Teach</span>
                    <i class="fas fa-arrow-right step-arrow"></i>
                    <span>Mentor</span>
                    <i class="fas fa-arrow-right step-arrow"></i>
                </div>
                <div class="approach-step">
                    <span class="step-icon"><i class="fas fa-chart-line"></i></span>
                    <span>Track Progress</span>
                    <i class="fas fa-arrow-right step-arrow"></i>
                </div>
            </div>

            <div class="approach-quote">
                <p>"No child should be denied the opportunity to build a better future because of a lack of educational support."</p>
            </div>

            <div class="approach-cta">
                <a href="contact.php" class="btn btn-primary btn-lg">Support Education <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

    </div>
</section>

<?php
include 'includes/footer.php';
?>