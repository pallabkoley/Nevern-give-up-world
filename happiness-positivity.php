<?php
// happiness-positivity.php

// Define page-specific CSS
$extraCSS = [
    'assets/css/happiness-positivity.css'
];

// Include DB connection
require_once 'includes/db.php';

// Initialize variables
$successMessage = '';
$errorMessage = '';
$formData = [
    'full_name'       => '',
    'dob'             => '',
    'gender'          => '',
    'mobile'          => '',
    'whatsapp'        => '',
    'email'           => '',
    'city'            => '',
    'state'           => '',
    'current_role'    => '',
    'focus_area'      => '',
    'coaching_mode'   => '',
    'session_timing'  => '',
    'goals'           => '',
    'message'         => ''
];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['happiness_enroll_submit'])) {
    
    // Sanitize inputs
    foreach ($formData as $key => $value) {
        $formData[$key] = trim($_POST[$key] ?? '');
    }

    // Validation
    $errors = [];
    if (empty($formData['full_name']))    $errors[] = "Full Name is required.";
    if (empty($formData['dob']))          $errors[] = "Date of Birth is required.";
    if (empty($formData['gender']))       $errors[] = "Gender is required.";
    if (empty($formData['mobile']))       $errors[] = "Mobile Number is required.";
    if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "A valid Email Address is required.";
    if (empty($formData['city']))         $errors[] = "City is required.";
    if (empty($formData['state']))        $errors[] = "State is required.";
    if (empty($formData['current_role'])) $errors[] = "Current Role is required.";
    if (empty($formData['focus_area']))   $errors[] = "Preferred Coaching Focus Area is required.";

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO happiness_enrollments 
                    (full_name, dob, gender, mobile, whatsapp, email, city, state, current_role, focus_area, coaching_mode, session_timing, goals, message) 
                    VALUES 
                    (:full_name, :dob, :gender, :mobile, :whatsapp, :email, :city, :state, :current_role, :focus_area, :coaching_mode, :session_timing, :goals, :message)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':full_name'       => $formData['full_name'],
                ':dob'             => $formData['dob'],
                ':gender'          => $formData['gender'],
                ':mobile'          => $formData['mobile'],
                ':whatsapp'        => $formData['whatsapp'],
                ':email'           => $formData['email'],
                ':city'            => $formData['city'],
                ':state'           => $formData['state'],
                ':current_role'    => $formData['current_role'],
                ':focus_area'      => $formData['focus_area'],
                ':coaching_mode'   => $formData['coaching_mode'],
                ':session_timing'  => $formData['session_timing'],
                ':goals'           => $formData['goals'],
                ':message'         => $formData['message']
            ]);

            $successMessage = "Thank you for enrolling! We will contact you shortly with the next steps.";
            $formData = array_map(fn() => '', $formData);

        } catch (PDOException $e) {
            $errorMessage = "Something went wrong. Please try again later.";
        }
    } else {
        $errorMessage = implode("<br>", $errors);
    }
}

include 'includes/header.php';
?>

<!-- PROGRAM HERO SECTION -->
<section class="program-detail-hero happiness-hero">
    <picture>
        <source media="(max-width: 768px)" srcset="assets/images/happiness-positivity-banner-mobile.jpg">
        <img src="assets/images/happiness-positivity-banner.png" alt="Happiness & Positive Psychology Coaching">
    </picture>
</section>

<!-- FOR EVERYONE & FOCUS AREAS SECTION -->
<section class="everyone-focus-section">
    <div class="container everyone-focus-grid">
        
        <!-- Left: For Everyone -->
        <div class="everyone-box">
            <h3>For Everyone</h3>
            <p class="everyone-sub">Our programs can be relevant for:</p>
            <ul class="everyone-list">
                <li><i class="fas fa-check"></i> Students</li>
                <li><i class="fas fa-check"></i> Working professionals</li>
                <li><i class="fas fa-check"></i> Entrepreneurs</li>
                <li><i class="fas fa-check"></i> Parents</li>
                <li><i class="fas fa-check"></i> Senior citizens</li>
                <li><i class="fas fa-check"></i> Teachers</li>
                <li><i class="fas fa-check"></i> Employees</li>
                <li><i class="fas fa-check"></i> Communities</li>
                <li><i class="fas fa-check"></i> Anyone seeking personal growth</li>
            </ul>
        </div>

        <!-- Right: Our Focus Areas -->
        <div class="focus-areas-box">
            <div class="focus-header">
                <div class="focus-header-icon"><i class="fas fa-heart"></i></div>
                <h3>Our Focus Areas</h3>
            </div>
            <ul class="focus-areas-list">
                <li><span class="focus-area-icon"><i class="fas fa-smile"></i></span> Positive Mindset</li>
                <li><span class="focus-area-icon"><i class="fas fa-heart"></i></span> Happiness &amp; Well-being</li>
                <li><span class="focus-area-icon"><i class="fas fa-shield-alt"></i></span> Resilience</li>
                <li><span class="focus-area-icon"><i class="fas fa-star"></i></span> Confidence &amp; Self-Worth</li>
                <li><span class="focus-area-icon"><i class="fas fa-spa"></i></span> Mindfulness</li>
                <li><span class="focus-area-icon"><i class="fas fa-users"></i></span> Positive Relationships</li>
            </ul>
        </div>

    </div>
</section>

<!-- OBJECTIVE & CTA SECTION -->
<section class="objective-cta-section happiness-objective">
    <div class="container objective-cta-container">
        
        <div class="objective-content">
            <div class="objective-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <div class="objective-text">
                <h3>Our Objective</h3>
                <p>To help individuals develop practical tools for greater happiness, resilience and positive living.</p>
            </div>
        </div>

        <div class="objective-cta-btn">
            <button type="button" class="btn btn-primary btn-lg" id="openHappinessModal">
                Enrol Now <i class="fas fa-arrow-right"></i>
            </button>
        </div>

    </div>
</section>

<!-- ==============================================
     HAPPINESS ENROL MODAL
     ============================================== -->
<div class="enroll-modal-overlay" id="happinessModal">
    <div class="enroll-modal">
        
        <!-- Modal Header -->
        <div class="enroll-modal-header">
            <h2>Enrol Now – Happiness &amp; Positive Psychology Coaching</h2>
            <button type="button" class="enroll-modal-close" id="closeHappinessModal" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="enroll-modal-body">
            
            <!-- Success / Error Messages -->
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>

            <form action="happiness-positivity.php" method="POST" class="enroll-form" id="happinessForm">
                <input type="hidden" name="happiness_enroll_submit" value="1">

                <!-- Full Name -->
                <div class="form-group">
                    <label for="full_name">Full Name <span class="required">*</span></label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($formData['full_name']); ?>" required placeholder="Enter your full name">
                </div>

                <!-- DOB & Gender -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="dob">Date of Birth <span class="required">*</span></label>
                        <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($formData['dob']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender <span class="required">*</span></label>
                        <select id="gender" name="gender" required>
                            <option value="">-- Select --</option>
                            <option value="Male"   <?php echo ($formData['gender'] == 'Male')   ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($formData['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other"  <?php echo ($formData['gender'] == 'Other')  ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <!-- Mobile & WhatsApp -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="mobile">Mobile Number <span class="required">*</span></label>
                        <input type="tel" id="mobile" name="mobile" value="<?php echo htmlspecialchars($formData['mobile']); ?>" required placeholder="10-digit mobile number">
                    </div>
                    <div class="form-group">
                        <label for="whatsapp">WhatsApp Number</label>
                        <input type="tel" id="whatsapp" name="whatsapp" value="<?php echo htmlspecialchars($formData['whatsapp']); ?>" placeholder="If different from mobile">
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" required placeholder="Enter your email">
                </div>

                <!-- City & State -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City <span class="required">*</span></label>
                        <select id="city" name="city" required>
                            <option value="">-- Select --</option>
                            <option value="Kolkata"    <?php echo ($formData['city'] == 'Kolkata')    ? 'selected' : ''; ?>>Kolkata</option>
                            <option value="Howrah"     <?php echo ($formData['city'] == 'Howrah')     ? 'selected' : ''; ?>>Howrah</option>
                            <option value="Durgapur"   <?php echo ($formData['city'] == 'Durgapur')   ? 'selected' : ''; ?>>Durgapur</option>
                            <option value="Siliguri"   <?php echo ($formData['city'] == 'Siliguri')   ? 'selected' : ''; ?>>Siliguri</option>
                            <option value="Asansol"    <?php echo ($formData['city'] == 'Asansol')    ? 'selected' : ''; ?>>Asansol</option>
                            <option value="Other"      <?php echo ($formData['city'] == 'Other')      ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="state">State <span class="required">*</span></label>
                        <select id="state" name="state" required>
                            <option value="">-- Select --</option>
                            <option value="West Bengal"    <?php echo ($formData['state'] == 'West Bengal')    ? 'selected' : ''; ?>>West Bengal</option>
                            <option value="Bihar"          <?php echo ($formData['state'] == 'Bihar')          ? 'selected' : ''; ?>>Bihar</option>
                            <option value="Jharkhand"      <?php echo ($formData['state'] == 'Jharkhand')      ? 'selected' : ''; ?>>Jharkhand</option>
                            <option value="Odisha"         <?php echo ($formData['state'] == 'Odisha')         ? 'selected' : ''; ?>>Odisha</option>
                            <option value="Assam"          <?php echo ($formData['state'] == 'Assam')          ? 'selected' : ''; ?>>Assam</option>
                            <option value="Other"          <?php echo ($formData['state'] == 'Other')          ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <!-- Current Role -->
                <div class="form-group">
                    <label for="current_role">Your Current Role / Profession <span class="required">*</span></label>
                    <select id="current_role" name="current_role" required>
                        <option value="">-- Select --</option>
                        <option value="Student"                             <?php echo ($formData['current_role'] == 'Student')                             ? 'selected' : ''; ?>>Student</option>
                        <option value="Working Professional / Employee"     <?php echo ($formData['current_role'] == 'Working Professional / Employee')     ? 'selected' : ''; ?>>Working Professional / Employee</option>
                        <option value="Entrepreneur / Business Owner"       <?php echo ($formData['current_role'] == 'Entrepreneur / Business Owner')       ? 'selected' : ''; ?>>Entrepreneur / Business Owner</option>
                        <option value="Parent"                              <?php echo ($formData['current_role'] == 'Parent')                              ? 'selected' : ''; ?>>Parent</option>
                        <option value="Teacher"                             <?php echo ($formData['current_role'] == 'Teacher')                             ? 'selected' : ''; ?>>Teacher</option>
                        <option value="Senior Citizen"                      <?php echo ($formData['current_role'] == 'Senior Citizen')                      ? 'selected' : ''; ?>>Senior Citizen</option>
                        <option value="Other"                               <?php echo ($formData['current_role'] == 'Other')                               ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <!-- Focus Area -->
                <div class="form-group">
                    <label for="focus_area">Select Your Preferred Coaching Focus Area <span class="required">*</span></label>
                    <select id="focus_area" name="focus_area" required>
                        <option value="">-- Select --</option>
                        <option value="Positive Mindset"                    <?php echo ($formData['focus_area'] == 'Positive Mindset')                    ? 'selected' : ''; ?>>Positive Mindset</option>
                        <option value="Happiness & Well-being"              <?php echo ($formData['focus_area'] == 'Happiness & Well-being')              ? 'selected' : ''; ?>>Happiness &amp; Well-being</option>
                        <option value="Resilience"                          <?php echo ($formData['focus_area'] == 'Resilience')                          ? 'selected' : ''; ?>>Resilience</option>
                        <option value="Confidence & Self-Worth"             <?php echo ($formData['focus_area'] == 'Confidence & Self-Worth')             ? 'selected' : ''; ?>>Confidence &amp; Self-Worth</option>
                        <option value="Mindfulness"                         <?php echo ($formData['focus_area'] == 'Mindfulness')                         ? 'selected' : ''; ?>>Mindfulness</option>
                        <option value="Positive Relationships"              <?php echo ($formData['focus_area'] == 'Positive Relationships')              ? 'selected' : ''; ?>>Positive Relationships</option>
                        <option value="Personal Growth & Development"       <?php echo ($formData['focus_area'] == 'Personal Growth & Development')       ? 'selected' : ''; ?>>Personal Growth &amp; Development</option>
                    </select>
                </div>

                <!-- Coaching Mode & Session Timing -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="coaching_mode">Preferred Coaching Mode</label>
                        <select id="coaching_mode" name="coaching_mode">
                            <option value="">-- Select --</option>
                            <option value="Online"  <?php echo ($formData['coaching_mode'] == 'Online')  ? 'selected' : ''; ?>>Online</option>
                            <option value="Offline" <?php echo ($formData['coaching_mode'] == 'Offline') ? 'selected' : ''; ?>>Offline</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="session_timing">Preferred Session Timing</label>
                        <select id="session_timing" name="session_timing">
                            <option value="">-- Select --</option>
                            <option value="Morning"   <?php echo ($formData['session_timing'] == 'Morning')   ? 'selected' : ''; ?>>Morning</option>
                            <option value="Afternoon" <?php echo ($formData['session_timing'] == 'Afternoon') ? 'selected' : ''; ?>>Afternoon</option>
                            <option value="Evening"   <?php echo ($formData['session_timing'] == 'Evening')   ? 'selected' : ''; ?>>Evening</option>
                            <option value="Weekends"  <?php echo ($formData['session_timing'] == 'Weekends')  ? 'selected' : ''; ?>>Weekends</option>
                        </select>
                    </div>
                </div>

                <!-- Goals -->
                <div class="form-group">
                    <label for="goals">What Would You Like to Achieve Through This Program?</label>
                    <textarea id="goals" name="goals" rows="3" placeholder="Share your goals, interests, or expectations..."><?php echo htmlspecialchars($formData['goals']); ?></textarea>
                </div>

                <!-- Message -->
                <div class="form-group">
                    <label for="message">Your Message / Additional Information</label>
                    <textarea id="message" name="message" rows="2" placeholder="Enter your message here..."><?php echo htmlspecialchars($formData['message']); ?></textarea>
                </div>

                <!-- Declaration -->
                <div class="form-group declaration-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="declaration" required>
                        <span>I confirm that the information provided is accurate and consent to being contacted by Never Give Up World Foundation regarding the Happiness &amp; Positive Psychology Coaching Program.</span>
                    </label>
                </div>

                <!-- Submit -->
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-submit">
                        Submit <i class="fas fa-paper-plane"></i>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php
// Auto-open modal if form was submitted with errors or success
if (!empty($errorMessage) || !empty($successMessage)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('happinessModal');
        if (modal) {
            modal.classList.add('active');
            document.body.classList.add('modal-open');
        }
    });
</script>
<?php endif; ?>

<?php
include 'includes/footer.php';
?>