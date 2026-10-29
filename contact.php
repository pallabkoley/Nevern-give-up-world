<?php
// contact.php - Contact Us Page
session_start();
require_once __DIR__ . '/includes/seo-config.php';   // gives us $site and e()
require_once __DIR__ . '/includes/db.php';

/* ---------- SEO variables (read by includes/header.php) ---------- */
$pageTitle       = 'Contact Us - Kolkata Office';
$metaDescription = 'Contact Never Give Up World in Salt Lake Sector V, Kolkata. Ask about our skill development, child education, capacity building and positive psychology programs, or partner with us.';
$metaKeywords    = 'contact Never Give Up World, NGO Kolkata contact, NGO Salt Lake Sector V, volunteer Kolkata, CSR partnership Kolkata';
$pageType        = 'ContactPage';
$breadcrumbs     = [['Contact Us', '/contact']];
$extraCSS        = ['/assets/css/contact.css'];

// Extra details merged into the NGO entity (same @id as the header's Organization node)
$extraSchema = [[
    '@type'  => ['NGO', 'Organization'],
    '@id'    => $site['url'] . '/#organization',
    'geo'    => ['@type' => 'GeoCoordinates', 'latitude' => 22.5700392, 'longitude' => 88.4338738],
    'hasMap' => 'https://www.google.com/maps/search/?api=1&query=22.5700392,88.4338738',
    'openingHoursSpecification' => [[
        '@type'     => 'OpeningHoursSpecification',
        'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        'opens'     => '09:30',
        'closes'    => '18:00',
    ]],
]];

/* ---------- Static lists (also used for server-side validation) ---------- */
$states = [
    'States' => ['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana',
        'Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya',
        'Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura',
        'Uttar Pradesh','Uttarakhand','West Bengal'],
    'Union Territories' => ['Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu',
        'Delhi','Jammu and Kashmir','Ladakh','Lakshadweep','Puducherry'],
];
$allStates = array_merge($states['States'], $states['Union Territories']);
$genders   = ['Male', 'Female', 'Other'];
$subjects  = [
    'Logistics - Skill Development Program',
    'Capacity Building & Skills Training',
    'Child Education',
    'Happiness and Positive Psychology Coaching',
];

/* ---------- State ---------- */
$errors = [];
$successMessage = '';
$formData = array_fill_keys(['full_name','dob','gender','mobile','whatsapp','email','city','state','subject','message'], '');

if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }

// Success message after redirect (Post/Redirect/Get - prevents duplicate submissions on refresh)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['sent'])) {
    $successMessage = 'Thank you for reaching out! We will get back to you shortly.';
}

/* ---------- Handle submission ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($formData as $key => $v) { $formData[$key] = trim((string)($_POST[$key] ?? '')); }

    // Spam trap: real users never fill this hidden field -> pretend success
    if (!empty($_POST['website'])) {
        header('Location: /contact?sent=1', true, 303);
        exit;
    }

    if (!hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? ''))) {
        $errors[] = 'Your session expired. Please reload the page and try again.';
    }

    if ($formData['full_name'] === '' || mb_strlen($formData['full_name']) > 100) $errors[] = 'Full Name is required (max 100 characters).';

    $dob = DateTime::createFromFormat('Y-m-d', $formData['dob']);
    if (!$dob || $dob->format('Y-m-d') !== $formData['dob'] || $dob > new DateTime('today') || $dob < new DateTime('1900-01-01'))
        $errors[] = 'A valid Date of Birth is required.';

    if (!in_array($formData['gender'], $genders, true)) $errors[] = 'Gender is required.';

    $mobile = preg_replace('/[\s-]/', '', $formData['mobile']);
    if (!preg_match('/^\+?\d{10,13}$/', $mobile)) $errors[] = 'A valid Mobile Number is required.';

    if ($formData['whatsapp'] !== '' && !preg_match('/^\+?\d{10,13}$/', preg_replace('/[\s-]/', '', $formData['whatsapp'])))
        $errors[] = 'WhatsApp Number looks invalid.';

    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($formData['email']) > 150) $errors[] = 'A valid Email Address is required.';
    if (!in_array($formData['state'], $allStates, true)) $errors[] = 'State is required.';
    if ($formData['city'] === '' || mb_strlen($formData['city']) > 100) $errors[] = 'City is required.';
    if (!in_array($formData['subject'], $subjects, true)) $errors[] = 'Subject is required.';
    if ($formData['message'] === '' || mb_strlen($formData['message']) > 3000) $errors[] = 'Message is required (max 3000 characters).';

    if (!$errors) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO contact_messages (full_name, dob, gender, mobile, whatsapp, email, city, state, subject, message)
                 VALUES (:full_name, :dob, :gender, :mobile, :whatsapp, :email, :city, :state, :subject, :message)"
            );
            $stmt->execute([
                ':full_name' => $formData['full_name'], ':dob'     => $formData['dob'],
                ':gender'    => $formData['gender'],    ':mobile'  => $formData['mobile'],
                ':whatsapp'  => $formData['whatsapp'],  ':email'   => $formData['email'],
                ':city'      => $formData['city'],      ':state'   => $formData['state'],
                ':subject'   => $formData['subject'],   ':message' => $formData['message'],
            ]);
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
            header('Location: /contact?sent=1', true, 303);
            exit;
        } catch (PDOException $e) {
            error_log('Contact form DB error: ' . $e->getMessage());
            $errors[] = 'Something went wrong. Please try again later.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- CONTACT HERO SECTION -->
<section class="contact-hero">
    <picture>
        <source media="(max-width: 768px)" srcset="/assets/images/contact-banner-mobile.jpg">
        <img src="/assets/images/contact-banner.png"
             alt="Contact Never Give Up World, Kolkata"
             width="1920" height="500" fetchpriority="high" decoding="async">
    </picture>
    <!-- The one H1 of the page (visually hidden; the banner image carries the visible text) -->
    <h1 class="visually-hidden">Contact Never Give Up World - NGO in Kolkata</h1>
</section>

<!-- CONTACT FORM & MAP SECTION -->
<section class="contact-main-section">
    <div class="container contact-grid">

        <!-- LEFT: Contact Form -->
        <div class="contact-form-wrapper">
            <h2>PLEASE GET IN TOUCH</h2>
            <p class="form-subtitle">Your email address will not be published</p>

            <?php if ($successMessage): ?>
                <div class="alert alert-success" role="status">
                    <i class="fas fa-check-circle" aria-hidden="true"></i> <?php echo e($successMessage); ?>
                </div>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="alert alert-error" role="alert">
                    <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                    <?php echo implode('<br>', array_map('e', $errors)); ?>
                </div>
            <?php endif; ?>

            <form action="/contact" method="POST" class="contact-form">
                <input type="hidden" name="csrf" value="<?php echo e($_SESSION['csrf']); ?>">

                <!-- Honeypot (hidden from humans) -->
                <div style="position:absolute;left:-9999px;" aria-hidden="true">
                    <label>Leave this empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name <span class="required">*</span></label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo e($formData['full_name']); ?>" required maxlength="100" autocomplete="name" placeholder="Enter your full name">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="dob">Date of Birth <span class="required">*</span></label>
                        <input type="date" id="dob" name="dob" value="<?php echo e($formData['dob']); ?>" required min="1900-01-01" max="<?php echo date('Y-m-d'); ?>" autocomplete="bday">
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender <span class="required">*</span></label>
                        <select id="gender" name="gender" required>
                            <option value="">-- Select --</option>
                            <?php foreach ($genders as $g): ?>
                                <option value="<?php echo e($g); ?>"<?php echo $formData['gender'] === $g ? ' selected' : ''; ?>><?php echo e($g); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="mobile">Mobile Number <span class="required">*</span></label>
                        <input type="tel" id="mobile" name="mobile" value="<?php echo e($formData['mobile']); ?>" required inputmode="tel" maxlength="16" autocomplete="tel" placeholder="10-digit mobile number">
                    </div>
                    <div class="form-group">
                        <label for="whatsapp">WhatsApp Number</label>
                        <input type="tel" id="whatsapp" name="whatsapp" value="<?php echo e($formData['whatsapp']); ?>" inputmode="tel" maxlength="16" placeholder="If different from mobile">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="<?php echo e($formData['email']); ?>" required maxlength="150" autocomplete="email" placeholder="Enter your email">
                </div>

                <!-- State & City (city list is loaded by /assets/js/contact.js) -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="state">State / Union Territory <span class="required">*</span></label>
                        <select id="state" name="state" required autocomplete="address-level1" data-selected="<?php echo e($formData['state']); ?>">
                            <option value="">-- Select State / UT --</option>
                            <?php foreach ($states as $group => $list): ?>
                                <optgroup label="<?php echo e($group); ?>">
                                    <?php foreach ($list as $s): ?>
                                        <option value="<?php echo e($s); ?>"<?php echo $formData['state'] === $s ? ' selected' : ''; ?>><?php echo e($s); ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city">City <span class="required">*</span></label>
                        <select id="city" name="city" required disabled data-selected="<?php echo e($formData['city']); ?>">
                            <option value="">-- Select State First --</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="subject">Subject <span class="required">*</span></label>
                    <select id="subject" name="subject" required>
                        <option value="">-- Select Subject --</option>
                        <?php foreach ($subjects as $s): ?>
                            <option value="<?php echo e($s); ?>"<?php echo $formData['subject'] === $s ? ' selected' : ''; ?>><?php echo e($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Your Message <span class="required">*</span></label>
                    <textarea id="message" name="message" rows="5" required maxlength="3000" placeholder="Write your message..."><?php echo e($formData['message']); ?></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-submit">
                        Submit <i class="fas fa-paper-plane" aria-hidden="true"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT: Google Map -->
        <div class="contact-map-wrapper">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3372.4247213096887!2d88.43387377488695!3d22.57003923306496!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a0275abe402075f%3A0xee21c315a8eca0ad!2sWest%20Bengal%20Electronics%20Industry%20Development%20Corporation%20Limited!5e1!3m2!1sen!2sin!4v1789990180728!5m2!1sen!2sin"
                title="Map showing the Never Give Up World office in Salt Lake Sector V, Kolkata"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen></iframe>
        </div>
    </div>
</section>

<!-- CONTACT INFO CARDS SECTION (matches NAP in the site-wide schema/footer) -->
<section class="contact-info-section" aria-label="Contact details">
    <div class="container contact-info-grid">

        <div class="info-card">
            <div class="info-icon"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></div>
            <h3>ADDRESS :</h3>
            <address style="font-style:normal;">
                Block - EP &amp; GP, Sector - V, Salt Lake City,<br>Kolkata, West Bengal - 700 091 (India).
            </address>
        </div>

        <div class="info-card">
            <div class="info-icon"><i class="fas fa-phone-alt" aria-hidden="true"></i></div>
            <h3>PHONE &amp; EMAIL :</h3>
            <p><a href="tel:+919883086911"><i class="fas fa-phone-alt" aria-hidden="true"></i> +91 98830 86911</a></p>
            <p><a href="mailto:contact@nevergiveupworld.com"><i class="fas fa-envelope" aria-hidden="true"></i> contact@nevergiveupworld.com</a></p>
        </div>

        <div class="info-card">
            <div class="info-icon"><i class="fas fa-clock" aria-hidden="true"></i></div>
            <h3>WORKING HOURS :</h3>
            <p>Monday - Saturday<br><time datetime="09:30">09:30</time> to <time datetime="18:00">18:00</time></p>
        </div>

    </div>
</section>

<script src="/assets/js/contact.js" defer></script>

<?php include __DIR__ . '/includes/footer.php'; ?>