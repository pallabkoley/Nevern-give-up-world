<?php
// our-impact.php - Our Impact Page
include 'includes/header.php';
?>

<!-- BREADCRUMB -->
<!--<div class="breadcrumb-section">-->
<!--    <div class="container">-->
<!--        <ul class="breadcrumb">-->
<!--            <li><a href="index.php">Home</a></li>-->
<!--            <li><i class="fas fa-chevron-right"></i></li>-->
<!--            <li><a href="about.php">About Us</a></li>-->
<!--            <li><i class="fas fa-chevron-right"></i></li>-->
<!--            <li>Our Impact</li>-->
<!--        </ul>-->
<!--    </div>-->
<!--</div>-->

<!-- IMPACT HERO SECTION -->
<section class="program-detail-hero impact-hero">
    <div class="container program-detail-hero-container">
        <!--<div class="program-detail-hero-content">-->
        <!--    <h1>Our Impact</h1>-->
        <!--    <h2>Creating Change Through People.</h2>-->
        <!--    <p>We believe impact should be measurable. Every number represents a person, a possibility and a step toward a better future.</p>-->
        <!--</div>-->
        <!--<div class="program-detail-hero-image">-->
        <!--    <img src="assets/images/our-impact-hero.jpg" alt="Our Impact">-->
        <!--</div>-->
    </div>
</section>

<!-- IMPACT STATS SECTION -->
<section class="impact-stats-section">
    <div class="container">
        <div class="impact-stats-grid">
            
            <!-- Youth Trained -->
            <div class="impact-stat-card">
                <div class="impact-stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="impact-stat-number">230+</div>
                <div class="impact-stat-label">Youth Trained</div>
            </div>

            <!-- Individuals Supported -->
            <div class="impact-stat-card">
                <div class="impact-stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="impact-stat-number">185+</div>
                <div class="impact-stat-label">Individuals Supported</div>
            </div>

            <!-- Children Supported -->
            <div class="impact-stat-card">
                <div class="impact-stat-icon">
                    <i class="fas fa-child"></i>
                </div>
                <div class="impact-stat-number">88+</div>
                <div class="impact-stat-label">Children Supported</div>
            </div>

            <!-- Small Businesses Supported -->
            <div class="impact-stat-card">
                <div class="impact-stat-icon">
                    <i class="fas fa-store"></i>
                </div>
                <div class="impact-stat-number">43+</div>
                <div class="impact-stat-label">Small Businesses Supported</div>
            </div>

            <!-- Communities Reached -->
            <div class="impact-stat-card">
                <div class="impact-stat-icon">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="impact-stat-number">22+</div>
                <div class="impact-stat-label">Communities Reached</div>
            </div>

        </div>

        <!-- Impact Quote -->
        <div class="impact-quote">
            <p>"Every number represents a person, a possibility and a step toward a better future."</p>
        </div>

    </div>
</section>

<!-- CTA SECTION -->
<section class="objective-cta-section">
    <div class="container objective-cta-container">
        <div class="objective-content">
            <div class="objective-icon">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <div class="objective-text">
                <h3>Help Us Create Greater Impact</h3>
                <p>Your support helps us reach more people, train more youth, and strengthen more communities.</p>
            </div>
        </div>
        <div class="objective-cta-btn">
            <a href="get-involved.php" class="btn btn-primary btn-lg">Get Involved <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>