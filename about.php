<?php
// about.php - About Us Page

/* ---------- SEO variables (read by includes/header.php) ---------- */
$pageTitle       = 'About Us - Mission, Vision & Approach';
$metaDescription = 'Learn about Never Give Up World, a Kolkata NGO empowering youth, small businesses and children through skills, education, capacity building and positive well-being.';
$metaKeywords    = 'about Never Give Up World, NGO Kolkata, NGO mission and vision, skill development NGO, youth employability, child education, capacity building, social impact India';
$pageType        = 'AboutPage';
$breadcrumbs     = [['About Us', '/about']];
$extraCSS        = ['/assets/css/about.css'];

include 'includes/header.php';
?>

<!-- ABOUT HERO SECTION -->
<section class="about-hero-section">
    <picture>
        <source media="(max-width: 768px)" srcset="/assets/images/about-hero-mobile.jpg">
        <!-- Set width/height to the image's real pixel size -->
        <img src="/assets/images/about-hero.jpg"
             alt="About Never Give Up World - empowering people through skills, education and well-being"
             width="1920" height="500" fetchpriority="high" decoding="async">
    </picture>
    <!-- The one H1 of the page (visually hidden; the banner image carries the visible text) -->
    <h1 class="visually-hidden">About Never Give Up World - Empowering People, Building Skills</h1>
</section>

<!-- TABBED CONTENT SECTION (Who We Are, Approach, Mission, Impact) -->
<!-- Content of inactive tabs is still in the HTML, so Google can read all of it. -->
<section class="about-tabs-section" id="aboutTabs" style="scroll-margin-top:100px;">
    <div class="container">
        <h2 class="visually-hidden">Who We Are, Our Approach, Our Mission and Our Impact</h2>

        <!-- Tab Navigation -->
        <div class="tabs-nav" role="tablist" aria-label="About Never Give Up World">
            <button type="button" class="tab-btn active" data-target="who-we-are"   role="tab" id="tab-who-we-are"   aria-controls="who-we-are"   aria-selected="true">Who We Are</button>
            <button type="button" class="tab-btn"        data-target="our-approach"  role="tab" id="tab-our-approach"  aria-controls="our-approach"  aria-selected="false">Our Approach</button>
            <button type="button" class="tab-btn"        data-target="our-mission"   role="tab" id="tab-our-mission"   aria-controls="our-mission"   aria-selected="false">Our Mission</button>
            <button type="button" class="tab-btn"        data-target="our-impact"    role="tab" id="tab-our-impact"    aria-controls="our-impact"    aria-selected="false">Our Impact</button>
        </div>

        <!-- Tab Content Panes -->
        <div class="tabs-content">

            <!-- TAB 1: WHO WE ARE -->
            <div class="tab-pane active" id="who-we-are" role="tabpanel" aria-labelledby="tab-who-we-are">
                <div class="tab-pane-grid">
                    <div class="tab-text">
                        <h3>Investing in People. Creating Possibilities.</h3>
                        <p>At Never Give Up World, we believe that sustainable social change begins by investing in people.</p>
                        <p>A young person needs the right skills to find meaningful employment.<br>
                        A small business needs the right capabilities to grow.<br>
                        A child needs a strong educational foundation to build a better future.<br>
                        And every individual deserves the opportunity to experience greater happiness, resilience and well-being.</p>
                        <p>Our programs are therefore designed around four simple objectives:<br>
                        <strong>Learn. Grow. Empower. Thrive.</strong></p>
                        <p>We create practical learning opportunities, strengthen capabilities, support foundational education and promote positive mental well-being.</p>
                        <p>Our approach combines training, mentoring, practical knowledge, community engagement and partnerships to create sustainable impact.</p>
                        <p>Explore our <a href="/programs">programs</a> or find out how you can <a href="/get-involved">get involved</a>.</p>
                    </div>
                    <div class="tab-image">
                        <img src="/assets/images/who-we-are.jpg"
                             alt="Never Give Up World team and participants at a skills and education program"
                             width="600" height="450" loading="lazy" decoding="async">
                    </div>
                </div>
            </div>

            <!-- TAB 2: OUR APPROACH -->
            <div class="tab-pane" id="our-approach" role="tabpanel" aria-labelledby="tab-our-approach">
                <h3 class="tab-title">Practical Learning. Meaningful Impact.</h3>
                <p class="tab-subtitle">We believe training and social programs should create real-world outcomes, not just certificates or attendance numbers.</p>
                <p class="tab-subtitle">Our approach is built around five steps:</p>

                <div class="approach-grid">
                    <div class="approach-step">
                        <span class="step-number" aria-hidden="true">01</span>
                        <h4>Understand</h4>
                        <p>Identify the needs of individuals, businesses and communities.</p>
                    </div>
                    <div class="approach-step">
                        <span class="step-number" aria-hidden="true">02</span>
                        <h4>Learn</h4>
                        <p>Provide structured, practical and accessible learning.</p>
                    </div>
                    <div class="approach-step">
                        <span class="step-number" aria-hidden="true">03</span>
                        <h4>Practice</h4>
                        <p>Connect learning with real-life situations and activities.</p>
                    </div>
                    <div class="approach-step">
                        <span class="step-number" aria-hidden="true">04</span>
                        <h4>Empower</h4>
                        <p>Build confidence, capability and independence.</p>
                    </div>
                    <div class="approach-step">
                        <span class="step-number" aria-hidden="true">05</span>
                        <h4>Impact</h4>
                        <p>Track progress and continuously improve our programs.</p>
                    </div>
                </div>
            </div>

            <!-- TAB 3: OUR MISSION -->
            <div class="tab-pane" id="our-mission" role="tabpanel" aria-labelledby="tab-our-mission">
                <div class="mission-vision-wrapper">
                    <div class="mv-block">
                        <h3>Our Mission</h3>
                        <p>To empower people through education, skills, capacity building and positive well-being, creating pathways toward greater opportunity, confidence and sustainable growth.</p>
                    </div>
                    <div class="mv-block">
                        <h3>Our Vision</h3>
                        <p class="vision-tagline">A World Where Every Person Has the Opportunity to Learn, Grow and Thrive.</p>
                        <p>We envision a society where:</p>
                        <ul class="vision-list">
                            <li><i class="fas fa-check-circle" aria-hidden="true"></i> Every young person can develop employable skills.</li>
                            <li><i class="fas fa-check-circle" aria-hidden="true"></i> Every small business can build capable teams.</li>
                            <li><i class="fas fa-check-circle" aria-hidden="true"></i> Every child has access to foundational education.</li>
                            <li><i class="fas fa-check-circle" aria-hidden="true"></i> Every individual can develop greater happiness and resilience.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- TAB 4: OUR IMPACT -->
            <div class="tab-pane" id="our-impact" role="tabpanel" aria-labelledby="tab-our-impact">
                <h3 class="tab-title">Creating Change Through People</h3>
                <p class="tab-subtitle">We believe impact should be measurable.</p>

                <div class="impact-stats-grid">
                    <div class="stat-card"><span class="stat-number">230+</span><span class="stat-label">Youth Trained</span></div>
                    <div class="stat-card"><span class="stat-number">185+</span><span class="stat-label">Individuals Supported</span></div>
                    <div class="stat-card"><span class="stat-number">88+</span><span class="stat-label">Children Supported</span></div>
                    <div class="stat-card"><span class="stat-number">43+</span><span class="stat-label">Small Businesses Supported</span></div>
                    <div class="stat-card"><span class="stat-number">22+</span><span class="stat-label">Communities Reached</span></div>
                </div>
                <p class="impact-footer-text">Every number represents a person, a possibility and a step toward a better future.</p>
            </div>

        </div>
    </div>
</section>

<!-- WHOM WE SERVE SECTION -->
<section class="whom-we-serve-section">
    <div class="container">
        <h2 class="section-title">Whom We Serve</h2>
        <div class="serve-grid">

            <div class="serve-card">
                <div class="serve-icon"><i class="fas fa-user-graduate" aria-hidden="true"></i></div>
                <h3>Youth</h3>
                <p>Helping young people develop skills and prepare for employment.</p>
            </div>

            <div class="serve-card">
                <div class="serve-icon"><i class="fas fa-briefcase" aria-hidden="true"></i></div>
                <h3>Unemployed Individuals</h3>
                <p>Creating pathways toward employability and career development.</p>
            </div>

            <div class="serve-card">
                <div class="serve-icon"><i class="fas fa-store" aria-hidden="true"></i></div>
                <h3>Small Businesses</h3>
                <p>Strengthening people capabilities and workplace performance.</p>
            </div>

            <div class="serve-card">
                <div class="serve-icon"><i class="fas fa-child" aria-hidden="true"></i></div>
                <h3>Children</h3>
                <p>Supporting foundational and primary education.</p>
            </div>

            <div class="serve-card">
                <div class="serve-icon"><i class="fas fa-globe" aria-hidden="true"></i></div>
                <h3>Individuals &amp; Communities</h3>
                <p>Promoting happiness, resilience and positive well-being.</p>
            </div>

        </div>
    </div>
</section>

<!-- WHY NEVER GIVE UP WORLD SECTION -->
<section class="why-us-section">
    <div class="container">
        <h2 class="section-title">Why Never Give Up World?</h2>
        <div class="why-grid">

            <div class="why-card">
                <i class="fas fa-industry" aria-hidden="true"></i>
                <h3>Industry-Relevant</h3>
                <p>Our programs focus on practical skills and real-world needs.</p>
            </div>

            <div class="why-card">
                <i class="fas fa-users" aria-hidden="true"></i>
                <h3>Inclusive</h3>
                <p>Our programs are designed for different age groups and communities.</p>
            </div>

            <div class="why-card">
                <i class="fas fa-tools" aria-hidden="true"></i>
                <h3>Practical</h3>
                <p>We focus on knowledge that participants can apply in everyday life.</p>
            </div>

            <div class="why-card">
                <i class="fas fa-heart" aria-hidden="true"></i>
                <h3>People-Centric</h3>
                <p>Our work starts with understanding individual and community needs.</p>
            </div>

            <div class="why-card">
                <i class="fas fa-handshake" aria-hidden="true"></i>
                <h3>Partnership-Driven</h3>
                <p>We collaborate with businesses, institutions, professionals and communities.</p>
            </div>

            <div class="why-card">
                <i class="fas fa-chart-line" aria-hidden="true"></i>
                <h3>Impact-Focused</h3>
                <p>Our goal is meaningful and measurable change.</p>
            </div>

        </div>
    </div>
</section>

<!-- TAGLINE BANNER (Learn. Grow. Empower. Thrive.) -->
<section class="tagline-banner-about">
    <div class="container tagline-about-container">
        <div class="tagline-about-text">
            <h2>Learn. Grow.<br>Empower. Thrive.</h2>
        </div>
    </div>
</section>

<!-- Deep-link support: the header menu links to /about#our-mission etc. -->
<!-- This opens the matching tab (re-using your existing tab click handler) and keeps aria-selected in sync. -->
<script>
(function () {
    var btns = document.querySelectorAll('.tab-btn');
    if (!btns.length) return;

    function syncAria() {
        btns.forEach(function (b) { b.setAttribute('aria-selected', b.classList.contains('active') ? 'true' : 'false'); });
    }
    btns.forEach(function (b) { b.addEventListener('click', function () { setTimeout(syncAria, 0); }); });

    function openFromHash() {
        var id = location.hash.replace('#', '');
        if (!id) return;
        var btn = document.querySelector('.tab-btn[data-target="' + id + '"]');
        if (btn) {
            btn.click();
            var box = document.getElementById('aboutTabs');
            if (box) box.scrollIntoView();
        }
    }
    window.addEventListener('hashchange', openFromHash);
    window.addEventListener('load', openFromHash);
})();
</script>

<?php
include 'includes/footer.php';
?>