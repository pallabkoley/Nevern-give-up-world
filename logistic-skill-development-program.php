<?php
// logistic-skill-development-program.php

// Define page-specific CSS
$extraCSS = [
    'assets/css/logistic-skill-development.css'
];

// Include DB connection
require_once 'includes/db.php';

// Initialize variables
$successMessage = '';
$errorMessage = '';
$formData = [
    'full_name'         => '',
    'dob'               => '',
    'gender'            => '',
    'mobile'            => '',
    'whatsapp'          => '',
    'email'             => '',
    'address'           => '',
    'city'              => '',
    'state'             => '',
    'pincode'           => '',
    'qualification'     => '',
    'employment_status' => '',
    'source'            => ''
];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_submit'])) {
    
    // Sanitize inputs
    foreach ($formData as $key => $value) {
        $formData[$key] = trim($_POST[$key] ?? '');
    }

    // Validation
    $errors = [];
    if (empty($formData['full_name']))         $errors[] = "Full Name is required.";
    if (empty($formData['dob']))               $errors[] = "Date of Birth is required.";
    if (empty($formData['gender']))            $errors[] = "Gender is required.";
    if (empty($formData['mobile']))            $errors[] = "Mobile Number is required.";
    if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "A valid Email Address is required.";
    if (empty($formData['address']))           $errors[] = "Full Address is required.";
    if (empty($formData['city']))              $errors[] = "City is required.";
    if (empty($formData['state']))             $errors[] = "State is required.";
    if (empty($formData['pincode']))           $errors[] = "PIN Code is required.";
    if (empty($formData['qualification']))     $errors[] = "Educational Qualification is required.";
    if (empty($formData['employment_status'])) $errors[] = "Current Employment Status is required.";

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO enrollments 
                    (full_name, dob, gender, mobile, whatsapp, email, address, city, state, pincode, qualification, employment_status, source) 
                    VALUES 
                    (:full_name, :dob, :gender, :mobile, :whatsapp, :email, :address, :city, :state, :pincode, :qualification, :employment_status, :source)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':full_name'         => $formData['full_name'],
                ':dob'               => $formData['dob'],
                ':gender'            => $formData['gender'],
                ':mobile'            => $formData['mobile'],
                ':whatsapp'          => $formData['whatsapp'],
                ':email'             => $formData['email'],
                ':address'           => $formData['address'],
                ':city'              => $formData['city'],
                ':state'             => $formData['state'],
                ':pincode'           => $formData['pincode'],
                ':qualification'     => $formData['qualification'],
                ':employment_status' => $formData['employment_status'],
                ':source'            => $formData['source']
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
<section class="program-detail-hero">
    <picture>
        <source media="(max-width: 768px)" srcset="assets/images/logistic-skill-development-banner-mobile.jpg">
        <img src="assets/images/logistic-skill-development-banner.png" alt="Logistics Skill Development Program">
    </picture>
</section>

<!-- TARGET AUDIENCE SECTION -->
<section class="target-audience-section">
    <div class="container target-audience-container">
        <div class="target-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="target-content">
            <h3>For Youth &amp; Unemployed Individuals</h3>
            <h4>Age 18 and Above</h4>
            <p>We help participants understand the logistics industry, develop practical workplace skills and become better prepared for employment and career opportunities.</p>
        </div>
    </div>
</section>

<!-- TRAINING & OBJECTIVE SECTION -->
<section class="training-objective-section">
    <div class="container training-objective-grid">
        
        <!-- Left: Training Areas -->
        <div class="training-areas-box">
            <h3>Training Areas</h3>
            <ul class="training-list">
                <li><span class="list-icon"><i class="fas fa-truck"></i></span> Transportation Management</li>
                <li><span class="list-icon"><i class="fas fa-warehouse"></i></span> Warehouse Management</li>
                <li><span class="list-icon"><i class="fas fa-shipping-fast"></i></span> Distribution &amp; Last Mile</li>
                <li><span class="list-icon"><i class="fas fa-link"></i></span> Supply Chain Fundamentals</li>
                <li><span class="list-icon"><i class="fas fa-briefcase"></i></span> Career &amp; Job Readiness</li>
            </ul>
        </div>

        <!-- Right: Objective & Learning Journey -->
        <div class="objective-journey-box">
            
            <!-- Objective -->
            <div class="objective-block">
                <div class="oj-icon"><i class="fas fa-bullseye"></i></div>
                <div class="oj-content">
                    <h3>Our Objective</h3>
                    <p>To bridge the gap between industry requirements and employable skills.</p>
                </div>
            </div>

            <!-- Learning Journey -->
            <div class="journey-block">
                <h3>Learning Journey</h3>
                <div class="journey-steps-visual">
                    <div class="journey-step">
                        <div class="step-icon"><i class="fas fa-user-graduate"></i></div>
                        <span>Learn<br>Basics</span>
                    </div>
                    <div class="journey-arrow"><i class="fas fa-arrow-right"></i></div>
                    <div class="journey-step">
                        <div class="step-icon"><i class="fas fa-clipboard-list"></i></div>
                        <span>Practice<br>Skills</span>
                    </div>
                    <div class="journey-arrow"><i class="fas fa-arrow-right"></i></div>
                    <div class="journey-step">
                        <div class="step-icon"><i class="fas fa-cogs"></i></div>
                        <span>Build<br>Confidence</span>
                    </div>
                    <div class="journey-arrow"><i class="fas fa-arrow-right"></i></div>
                    <div class="journey-step">
                        <div class="step-icon"><i class="fas fa-chart-bar"></i></div>
                        <span>Access<br>Jobs</span>
                    </div>
                    <div class="journey-arrow"><i class="fas fa-arrow-right"></i></div>
                    <div class="journey-step">
                        <div class="step-icon"><i class="fas fa-briefcase"></i></div>
                        <span>Create<br>Better Futures</span>
                    </div>
                </div>
            </div>

            <!-- CTA Button (opens the modal) -->
            <div class="journey-cta">
                <button type="button" class="btn btn-primary btn-lg" id="openEnrollModal">
                    Enrol Now <i class="fas fa-arrow-right"></i>
                </button>
            </div>

        </div>
    </div>
</section>

<!-- ==============================================
     ENROL NOW MODAL
     ============================================== -->
<div class="enroll-modal-overlay" id="enrollModal">
    <div class="enroll-modal">
        
        <!-- Modal Header -->
        <div class="enroll-modal-header">
            <h2>Enrol Now</h2>
            <button type="button" class="enroll-modal-close" id="closeEnrollModal" aria-label="Close">
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

            <form action="logistic-skill-development-program.php" method="POST" class="enroll-form" id="enrollForm">
                <input type="hidden" name="enroll_submit" value="1">

                <!-- Full Name -->
                <div class="form-group">
                    <label for="full_name">Full Name <span class="required">*</span></label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($formData['full_name']); ?>" required placeholder="Enter your full name">
                </div>

                <!-- Date of Birth & Gender -->
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
                    <label for="email">Email ID <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" required placeholder="Enter your email">
                </div>

                <!-- Full Address -->
                <div class="form-group">
                    <label for="address">Full Address <span class="required">*</span></label>
                    <textarea id="address" name="address" rows="2" required placeholder="Enter your full address"><?php echo htmlspecialchars($formData['address']); ?></textarea>
                </div>

                <!-- City, State, PIN -->
                <div class="form-row form-row-3">
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
                    <div class="form-group">
                        <label for="pincode">PIN Code <span class="required">*</span></label>
                        <input type="text" id="pincode" name="pincode" value="<?php echo htmlspecialchars($formData['pincode']); ?>" required placeholder="6-digit PIN">
                    </div>
                </div>

                <!-- Qualification & Employment Status -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="qualification">Educational Qualification <span class="required">*</span></label>
                        <select id="qualification" name="qualification" required>
                            <option value="">-- Select --</option>
                            <option value="Below 10th"   <?php echo ($formData['qualification'] == 'Below 10th')   ? 'selected' : ''; ?>>Below 10th</option>
                            <option value="10th"         <?php echo ($formData['qualification'] == '10th')         ? 'selected' : ''; ?>>10th</option>
                            <option value="12th"         <?php echo ($formData['qualification'] == '12th')         ? 'selected' : ''; ?>>12th</option>
                            <option value="Graduate"     <?php echo ($formData['qualification'] == 'Graduate')     ? 'selected' : ''; ?>>Graduate</option>
                            <option value="Postgraduate" <?php echo ($formData['qualification'] == 'Postgraduate') ? 'selected' : ''; ?>>Postgraduate</option>
                            <option value="Other"        <?php echo ($formData['qualification'] == 'Other')        ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="employment_status">Current Employment Status <span class="required">*</span></label>
                        <select id="employment_status" name="employment_status" required>
                            <option value="">-- Select --</option>
                            <option value="Student"    <?php echo ($formData['employment_status'] == 'Student')    ? 'selected' : ''; ?>>Student</option>
                            <option value="Fresher"    <?php echo ($formData['employment_status'] == 'Fresher')    ? 'selected' : ''; ?>>Fresher</option>
                            <option value="Employed"   <?php echo ($formData['employment_status'] == 'Employed')   ? 'selected' : ''; ?>>Employed</option>
                            <option value="Unemployed" <?php echo ($formData['employment_status'] == 'Unemployed') ? 'selected' : ''; ?>>Unemployed</option>
                        </select>
                    </div>
                </div>

                <!-- How did you hear about us -->
                <div class="form-group">
                    <label for="source">How did you hear about Never Give Up World Foundation?</label>
                    <select id="source" name="source">
                        <option value="">-- Select --</option>
                        <option value="Facebook"  <?php echo ($formData['source'] == 'Facebook')  ? 'selected' : ''; ?>>Facebook</option>
                        <option value="Instagram" <?php echo ($formData['source'] == 'Instagram') ? 'selected' : ''; ?>>Instagram</option>
                        <option value="LinkedIn"  <?php echo ($formData['source'] == 'LinkedIn')  ? 'selected' : ''; ?>>LinkedIn</option>
                        <option value="Website"   <?php echo ($formData['source'] == 'Website')   ? 'selected' : ''; ?>>Website</option>
                        <option value="Friends"   <?php echo ($formData['source'] == 'Friends')   ? 'selected' : ''; ?>>Friends</option>
                        <option value="Other"     <?php echo ($formData['source'] == 'Other')     ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <!-- Declaration -->
                <div class="form-group declaration-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="declaration" required>
                        <span>I confirm that the information provided above is accurate and complete. I agree to be contacted by Never Give Up World Foundation regarding course enrolment, training schedules, and career opportunities.</span>
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
<?php if (!empty($errorMessage) || !empty($successMessage)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('enrollModal');
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