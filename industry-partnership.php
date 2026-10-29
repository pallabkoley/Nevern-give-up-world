<?php
// industry-partnership.php - Industry Partnership Page

// Define page-specific CSS
$extraCSS = [
    'assets/css/industry-partnership.css'
];

include 'includes/header.php';
?>

<!-- INDUSTRY PARTNERSHIP HERO SECTION -->
<section class="industry-hero-section">
    <div class="container industry-hero-container">
        <div class="industry-hero-content">
            <h4 class="industry-subtitle">For Corporate &amp; Industry Partners</h4>
            <h1>From Training to Opportunity</h1>
            <p>For companies and industry partners, Never Give Up World can create structured partnerships that bridge the gap between industry requirements and employable skills.</p>
            <a href="partner.php" class="btn btn-primary">Become a Partner <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="industry-hero-image">
            <img src="assets/images/industry-partnership-hero.jpg" alt="Industry Partnership">
        </div>
    </div>
</section>

<!-- PARTNERSHIP AREAS SECTION -->
<section class="partnership-areas-section">
    <div class="container">
        <h2 class="section-title">Structured Partnerships Around</h2>
        <p class="section-subtitle">We collaborate with corporates and industry partners across the following focus areas:</p>

        <div class="partnership-areas-grid">
            
            <div class="area-card">
                <div class="area-icon"><i class="fas fa-hand-holding-heart"></i></div>
                <h3>CSR Skill Development</h3>
                <p>Support skill development initiatives that create measurable social impact.</p>
            </div>

            <div class="area-card">
                <div class="area-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <h3>Industry-Oriented Training</h3>
                <p>Co-create training programs aligned with real-world industry needs.</p>
            </div>

            <div class="area-card">
                <div class="area-icon"><i class="fas fa-users-cog"></i></div>
                <h3>Employee Soft-Skill Development</h3>
                <p>Enhance your team's communication, leadership and workplace productivity.</p>
            </div>

            <div class="area-card">
                <div class="area-icon"><i class="fas fa-store"></i></div>
                <h3>Small Business Capacity Building</h3>
                <p>Help small businesses grow through structured capacity-building programs.</p>
            </div>

            <div class="area-card">
                <div class="area-icon"><i class="fas fa-user-graduate"></i></div>
                <h3>Youth Employability</h3>
                <p>Prepare young people for meaningful employment through practical training.</p>
            </div>

            <div class="area-card">
                <div class="area-icon"><i class="fas fa-book-open"></i></div>
                <h3>Education Support</h3>
                <p>Support foundational and primary education for children in need.</p>
            </div>

            <div class="area-card">
                <div class="area-icon"><i class="fas fa-heartbeat"></i></div>
                <h3>Community Well-being</h3>
                <p>Promote happiness, resilience and positive mental well-being in communities.</p>
            </div>

        </div>
    </div>
</section>

<!-- LOGISTICS PARTNERS SECTION -->
<section class="logistics-partners-section">
    <div class="container logistics-partners-container">
        
        <div class="logistics-content">
            <h2>For Logistics &amp; Supply-Chain Organizations</h2>
            <p class="logistics-tagline">Help us prepare the next generation of logistics professionals.</p>
            <p>India's logistics and supply chain sector is creating increasing opportunities for skilled professionals across transportation, warehousing, distribution, e-commerce and last-mile operations. Your partnership can directly bridge the gap between industry requirements and employable skills.</p>
        </div>

        <div class="logistics-support-grid">
            <h3>Industry partners can support through:</h3>
            <div class="support-items-grid">
                <div class="support-item"><i class="fas fa-microphone"></i> Guest Lectures</div>
                <div class="support-item"><i class="fas fa-building"></i> Industry Visits</div>
                <div class="support-item"><i class="fas fa-user-friends"></i> Mentoring</div>
                <div class="support-item"><i class="fas fa-tools"></i> Training Support</div>
                <div class="support-item"><i class="fas fa-briefcase"></i> Internship Opportunities</div>
                <div class="support-item"><i class="fas fa-id-badge"></i> Job Opportunities</div>
                <div class="support-item"><i class="fas fa-book"></i> Curriculum Inputs</div>
                <div class="support-item"><i class="fas fa-donate"></i> CSR Funding</div>
            </div>
        </div>

    </div>
</section>

<!-- WHY PARTNER WITH US SECTION -->
<section class="why-partner-section">
    <div class="container">
        <h2 class="section-title">Why Partner With Never Give Up World?</h2>
        <div class="why-partner-grid">
            
            <div class="why-partner-card">
                <i class="fas fa-industry"></i>
                <h4>Industry-Relevant</h4>
                <p>Our programs focus on practical skills and real-world needs.</p>
            </div>

            <div class="why-partner-card">
                <i class="fas fa-users"></i>
                <h4>Inclusive</h4>
                <p>Our programs are designed for different age groups and communities.</p>
            </div>

            <div class="why-partner-card">
                <i class="fas fa-tools"></i>
                <h4>Practical</h4>
                <p>We focus on knowledge that participants can apply in everyday life.</p>
            </div>

            <div class="why-partner-card">
                <i class="fas fa-heart"></i>
                <h4>People-Centric</h4>
                <p>Our work starts with understanding individual and community needs.</p>
            </div>

            <div class="why-partner-card">
                <i class="fas fa-handshake"></i>
                <h4>Partnership-Driven</h4>
                <p>We collaborate with businesses, institutions, professionals and communities.</p>
            </div>

            <div class="why-partner-card">
                <i class="fas fa-chart-line"></i>
                <h4>Impact-Focused</h4>
                <p>Our goal is meaningful and measurable change.</p>
            </div>

        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="industry-cta-section">
    <div class="container">
        <h2>Let's Build the Next Generation of Professionals Together</h2>
        <p>Whether you are a corporate, a small business, an educational institution or an industry partner, there is a place for you in our journey.</p>
        <div class="industry-cta-buttons">
            <a href="partner.php" class="btn btn-primary btn-lg">Become a Partner <i class="fas fa-arrow-right"></i></a>
            <a href="contact.php" class="btn btn-outline btn-lg">Contact Us <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>