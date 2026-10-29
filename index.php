<?php
// index.php - Home Page

/* ---------- SEO variables (read by includes/header.php) ---------- */
$pageTitle       = 'NGO for Skills & Child Education in Kolkata';   // header appends " | Never Give Up World"
$metaDescription = 'Never Give Up World is a Kolkata NGO building skills through logistics training, soft skills, child education and positive psychology for a stronger tomorrow.';
$metaKeywords    = 'NGO in Kolkata, skill development NGO, logistics skill training, child education NGO, capacity building, soft skills training, positive psychology, Never Give Up World';
$pageType        = 'WebPage';
// Home page: no breadcrumbs needed.

// Structured data: list of the four focus areas (helps Google understand site structure)
$extraSchema = [[
    '@type'           => 'ItemList',
    '@id'             => 'https://nevergiveupworld.com/#focus-areas',
    'name'            => 'Our Focus Areas',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Logistics Skill Development',      'url' => 'https://nevergiveupworld.com/logistic-skill-development-program'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Capacity Building & Soft Skills',   'url' => 'https://nevergiveupworld.com/capacity-builiding-soft-skill'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Child Education',                    'url' => 'https://nevergiveupworld.com/child-education'],
        ['@type' => 'ListItem', 'position' => 4, 'name' => 'Happiness & Positive Psychology',    'url' => 'https://nevergiveupworld.com/happiness-positivity'],
    ],
]];

include 'includes/header.php';
?>

<!-- HERO SECTION -->
<section class="hero-section" aria-labelledby="home-heading">
    <picture>
        <source media="(max-width: 768px)" srcset="/assets/images/home-hero-mobile.jpg">
        <!-- Hero is the LCP element: never lazy-load it. Set width/height to the image's real pixel size. -->
        <img src="/assets/images/home-hero.jpg"
             alt="Never Give Up World - empowering people and building skills through education and training in Kolkata"
             width="1920" height="800"
             fetchpriority="high" decoding="async">
    </picture>
    <div class="container hero-container">
        <!-- The one and only H1 of the page (visually hidden because the hero image already carries the text) -->
        <h1 id="home-heading" class="visually-hidden">Never Give Up World - Empowering People, Building Skills in Kolkata</h1>
        <div class="hero-button-wrapper">
            <a href="/programs" class="btn btn-primary">Explore Our Programs <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </div>
</section>

<!-- INTRO (crawlable text + internal links; edit the copy to match your voice) -->
<section class="intro-section visually-hidden">
    <div class="container">
        <p class="intro-text" style="max-width:850px;margin:40px auto 0;text-align:center;">
            Never Give Up World is a social-impact organization based in Kolkata, West Bengal. Through
            <a href="/logistic-skill-development-program">logistics skill development</a>,
            <a href="/capacity-builiding-soft-skill">capacity building and soft skills training</a>,
            <a href="/child-education">child education</a> and
            <a href="/happiness-positivity">positive psychology programs</a>,
            we help people build skills, confidence and well-being. Learn more
            <a href="/about">about us</a>, or see how you can
            <a href="/volunteer">volunteer</a> or
            <a href="/support">support a program</a>.
        </p>
    </div>
</section>

<!-- FOCUS AREAS SECTION -->
<section class="focus-areas-section" aria-labelledby="focus-areas-heading">
    <div class="container">
        <h2 class="section-title" id="focus-areas-heading">Our Focus Areas</h2>
        <div class="focus-grid">

            <!-- Skill Development -->
            <a href="/logistic-skill-development-program" class="focus-card" title="Logistics Skill Development Program">
                <div class="focus-icon">
                    <i class="fas fa-hard-hat" aria-hidden="true"></i>
                </div>
                <h3>Skill Development</h3>
                <p>Logistics Industry</p>
            </a>

            <!-- Capacity Building -->
            <a href="/capacity-builiding-soft-skill" class="focus-card" title="Capacity Building and Soft Skill Training">
                <div class="focus-icon">
                    <i class="fas fa-users" aria-hidden="true"></i>
                </div>
                <h3>Capacity Building</h3>
                <p>&amp; Soft Skill Training</p>
            </a>

            <!-- Child Education -->
            <a href="/child-education" class="focus-card" title="Child Education Program">
                <div class="focus-icon">
                    <i class="fas fa-book-open" aria-hidden="true"></i>
                </div>
                <h3>Child Education</h3>
                <p>Primary Education</p>
            </a>

            <!-- Happiness -->
            <a href="/happiness-positivity" class="focus-card" title="Happiness and Positive Psychology Program">
                <div class="focus-icon">
                    <i class="fas fa-smile" aria-hidden="true"></i>
                </div>
                <!-- Full phrase kept inside the heading so search engines read "Happiness & Positive Psychology" -->
                <h3>Happiness &amp;<br>Positive Psychology</h3>
                <p>For Everyone</p>
            </a>

        </div>
    </div>
</section>

<!-- TAGLINE BANNER -->
<section class="tagline-banner">
    <div class="container">
        <h2>&ldquo;Skills. Education. Well-being.<br>A Stronger Tomorrow for All.&rdquo;</h2>
    </div>
</section>

<?php
include 'includes/footer.php';
?>