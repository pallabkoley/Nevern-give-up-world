<?php
// programs.php - Programs Page

// Define page-specific CSS
$extraCSS = [
    'assets/css/programs.css'
];

include 'includes/header.php';
?>

<!-- PROGRAMS HERO SECTION -->
<section class="programs-hero-section">
    <picture>
        <source media="(max-width: 768px)" srcset="assets/images/programe-banner-mobile.jpg">
        <img src="assets/images/programe-banner.jpg" alt="Our Programs">
    </picture>
</section>

<!-- PROGRAMS GRID SECTION -->
<section class="programs-grid-section">
    <div class="container">
        <div class="programs-grid">
            
            <!-- Program 1: Skill Development -->
            <div class="program-card">
                <div class="program-card-image">
                    <img src="assets/images/skill-development.png" alt="Skill Development Program">
                </div>
                <div class="program-card-content">
                    <h3>Skill Development Program</h3>
                    <h4 class="program-subtitle">Logistics Industry</h4>
                    <p>For youth &amp; unemployed individuals<br>(18–40 years)</p>
                    <a href="logistic-skill-development-program" class="btn btn-primary btn-sm">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Program 2: Capacity Building -->
            <div class="program-card">
                <div class="program-card-image">
                    <img src="assets/images/capacity-building.png" alt="Capacity Building & Soft Skill Training">
                </div>
                <div class="program-card-content">
                    <h3>Capacity Building &amp;<br>Soft Skill Training</h3>
                    <h4 class="program-subtitle">For small businesses and their teams</h4>
                    <p>&nbsp;</p>
                    <a href="capacity-builiding-soft-skill" class="btn btn-primary btn-sm">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Program 3: Child Education -->
            <div class="program-card">
                <div class="program-card-image">
                    <img src="assets/images/child-education.png" alt="Child Education">
                </div>
                <div class="program-card-content">
                    <h3>Child Education</h3>
                    <h4 class="program-subtitle">Primary Education</h4>
                    <p>For children with limited access<br>to learning resources</p>
                    <a href="child-education" class="btn btn-primary btn-sm">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Program 4: Happiness & Positive Psychology -->
            <div class="program-card">
                <div class="program-card-image">
                    <img src="assets/images/happiness-positive-psychology.jpg" alt="Happiness & Positive Psychology">
                </div>
                <div class="program-card-content">
                    <h3>Happiness &amp; Positive Psychology</h3>
                    <h4 class="program-subtitle">For Everyone</h4>
                    <p>Build a positive mindset, resilience<br>and well-being</p>
                    <a href="happiness-positivity" class="btn btn-primary btn-sm">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>