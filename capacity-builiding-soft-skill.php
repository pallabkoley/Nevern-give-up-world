<?php
// capacity-builiding-soft-skill.php

// Define page-specific CSS
$extraCSS = [
    'assets/css/capacity-building.css'
];

// Include DB connection
require_once 'includes/db.php';

// Initialize variables
$successMessage = '';
$errorMessage = '';
$formData = [
    'full_name'           => '',
    'dob'                 => '',
    'gender'              => '',
    'mobile'              => '',
    'whatsapp'            => '',
    'email'               => '',
    'city'                => '',
    'state'               => '',
    'professional_status' => '',
    'business_name'       => '',
    'designation'         => '',
    'industry_sector'     => '',
    'experience'          => '',
    'preferred_training'  => '',
    'customized_training' => '',
    'num_participants'    => '',
    'skills_to_improve'   => '',
    'business_challenges' => '',
    'expected_outcome'    => '',
    'source'              => ''
];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['capacity_enroll_submit'])) {
    
    // Sanitize inputs
    foreach ($formData as $key => $value) {
        $formData[$key] = trim($_POST[$key] ?? '');
    }

    // Validation
    $errors = [];
    if (empty($formData['full_name']))           $errors[] = "Full Name is required.";
    if (empty($formData['dob']))                 $errors[] = "Date of Birth is required.";
    if (empty($formData['gender']))              $errors[] = "Gender is required.";
    if (empty($formData['mobile']))              $errors[] = "Mobile Number is required.";
    if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "A valid Email Address is required.";
    if (empty($formData['city']))                $errors[] = "City is required.";
    if (empty($formData['state']))               $errors[] = "State is required.";
    if (empty($formData['professional_status'])) $errors[] = "Professional Status is required.";
    if (empty($formData['preferred_training']))  $errors[] = "Preferred Training Area is required.";

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO capacity_enrollments 
                    (full_name, dob, gender, mobile, whatsapp, email, city, state, professional_status, business_name, designation, industry_sector, experience, preferred_training, customized_training, num_participants, skills_to_improve, business_challenges, expected_outcome, source) 
                    VALUES 
                    (:full_name, :dob, :gender, :mobile, :whatsapp, :email, :city, :state, :professional_status, :business_name, :designation, :industry_sector, :experience, :preferred_training, :customized_training, :num_participants, :skills_to_improve, :business_challenges, :expected_outcome, :source)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':full_name'           => $formData['full_name'],
                ':dob'                 => $formData['dob'],
                ':gender'              => $formData['gender'],
                ':mobile'              => $formData['mobile'],
                ':whatsapp'            => $formData['whatsapp'],
                ':email'               => $formData['email'],
                ':city'                => $formData['city'],
                ':state'               => $formData['state'],
                ':professional_status' => $formData['professional_status'],
                ':business_name'       => $formData['business_name'],
                ':designation'         => $formData['designation'],
                ':industry_sector'     => $formData['industry_sector'],
                ':experience'          => $formData['experience'],
                ':preferred_training'  => $formData['preferred_training'],
                ':customized_training' => $formData['customized_training'],
                ':num_participants'    => $formData['num_participants'],
                ':skills_to_improve'   => $formData['skills_to_improve'],
                ':business_challenges' => $formData['business_challenges'],
                ':expected_outcome'    => $formData['expected_outcome'],
                ':source'              => $formData['source']
            ]);

            $successMessage = "Thank you for registering! We will contact you shortly with the next steps.";
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
<section class="program-detail-hero capacity-hero">
    <picture>
        <source media="(max-width: 768px)" srcset="assets/images/capability-building-banner-mobile.jpg">
        <img src="assets/images/Capability-building-banner.png" alt="Capacity Building & Soft Skill Training">
    </picture>
</section>

<!-- WHO CAN BENEFIT & TRAINING AREAS SECTION -->
<section class="benefit-training-section">
    <div class="container benefit-training-grid">
        
        <!-- Left: Who Can Benefit -->
        <div class="benefit-box">
            <div class="box-header">
                <div class="box-icon"><i class="fas fa-users"></i></div>
                <h3>Who Can Benefit?</h3>
            </div>
            <ul class="benefit-list">
                <li><span class="check-icon"><i class="fas fa-check"></i></span> Small business owners</li>
                <li><span class="check-icon"><i class="fas fa-check"></i></span> Start-ups</li>
                <li><span class="check-icon"><i class="fas fa-check"></i></span> Micro &amp; small enterprises</li>
                <li><span class="check-icon"><i class="fas fa-check"></i></span> Family-run businesses</li>
                <li><span class="check-icon"><i class="fas fa-check"></i></span> Supervisors</li>
                <li><span class="check-icon"><i class="fas fa-check"></i></span> Team leaders</li>
                <li><span class="check-icon"><i class="fas fa-check"></i></span> Frontline employees</li>
                <li><span class="check-icon"><i class="fas fa-check"></i></span> Sales &amp; customer-service teams</li>
            </ul>
        </div>

        <!-- Right: Training Areas -->
        <div class="training-areas-box capacity-training-box">
            <div class="box-header">
                <div class="box-icon dark-icon"><i class="fas fa-cog"></i></div>
                <h3>Our Training Areas</h3>
            </div>
            <ul class="training-list">
                <li><span class="list-icon"><i class="fas fa-comments"></i></span> Communication Skills</li>
                <li><span class="list-icon"><i class="fas fa-user"></i></span> Customer Service</li>
                <li><span class="list-icon"><i class="fas fa-users-cog"></i></span> Leadership &amp; Team Management</li>
                <li><span class="list-icon"><i class="fas fa-chart-bar"></i></span> Workplace Productivity</li>
                <li><span class="list-icon"><i class="fas fa-chart-line"></i></span> Sales &amp; Business Development</li>
                <li><span class="list-icon"><i class="fas fa-briefcase"></i></span> Sales &amp; Workplace Skills</li>
            </ul>
        </div>

    </div>
</section>

<!-- OBJECTIVE & CTA SECTION -->
<section class="objective-cta-section">
    <div class="container objective-cta-container">
        
        <div class="objective-content">
            <div class="objective-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <div class="objective-text">
                <h3>Our Objective</h3>
                <p>To help small businesses build capable people, stronger teams and better customer experiences.</p>
            </div>
        </div>

        <div class="objective-cta-btn">
            <button type="button" class="btn btn-primary btn-lg" id="openCapacityModal">
                Enrol Now <i class="fas fa-arrow-right"></i>
            </button>
        </div>

    </div>
</section>

<!-- ==============================================
     CAPACITY BUILDING ENROL MODAL
     ============================================== -->
<div class="enroll-modal-overlay" id="capacityModal">
    <div class="enroll-modal">
        
        <!-- Modal Header -->
        <div class="enroll-modal-header">
            <h2>Capacity Building &amp; Skills Training – Enrolment Form</h2>
            <button type="button" class="enroll-modal-close" id="closeCapacityModal" aria-label="Close">
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

            <form action="capacity-builiding-soft-skill.php" method="POST" class="enroll-form" id="capacityForm">
                <input type="hidden" name="capacity_enroll_submit" value="1">

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

                <!-- Professional Status -->
                <div class="form-group">
                    <label for="professional_status">Current Professional Status <span class="required">*</span></label>
                    <select id="professional_status" name="professional_status" required>
                        <option value="">-- Select --</option>
                        <option value="Small Business Owner"                <?php echo ($formData['professional_status'] == 'Small Business Owner')                ? 'selected' : ''; ?>>Small Business Owner</option>
                        <option value="Start-up Founder / Entrepreneur"     <?php echo ($formData['professional_status'] == 'Start-up Founder / Entrepreneur')     ? 'selected' : ''; ?>>Start-up Founder / Entrepreneur</option>
                        <option value="Micro & Small Enterprise Owner"      <?php echo ($formData['professional_status'] == 'Micro & Small Enterprise Owner')      ? 'selected' : ''; ?>>Micro & Small Enterprise Owner</option>
                        <option value="Family Business Member"              <?php echo ($formData['professional_status'] == 'Family Business Member')              ? 'selected' : ''; ?>>Family Business Member</option>
                        <option value="Supervisor"                          <?php echo ($formData['professional_status'] == 'Supervisor')                          ? 'selected' : ''; ?>>Supervisor</option>
                        <option value="Team Leader"                         <?php echo ($formData['professional_status'] == 'Team Leader')                         ? 'selected' : ''; ?>>Team Leader</option>
                        <option value="Frontline Employee"                  <?php echo ($formData['professional_status'] == 'Frontline Employee')                  ? 'selected' : ''; ?>>Frontline Employee</option>
                        <option value="Sales Professional"                  <?php echo ($formData['professional_status'] == 'Sales Professional')                  ? 'selected' : ''; ?>>Sales Professional</option>
                        <option value="Customer Service Professional"       <?php echo ($formData['professional_status'] == 'Customer Service Professional')       ? 'selected' : ''; ?>>Customer Service Professional</option>
                        <option value="Others"                              <?php echo ($formData['professional_status'] == 'Others')                              ? 'selected' : ''; ?>>Others</option>
                    </select>
                </div>

                <!-- Business Name & Designation -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="business_name">Business / Organization Name</label>
                        <input type="text" id="business_name" name="business_name" value="<?php echo htmlspecialchars($formData['business_name']); ?>" placeholder="Enter business name">
                    </div>
                    <div class="form-group">
                        <label for="designation">Your Designation / Job Role</label>
                        <input type="text" id="designation" name="designation" value="<?php echo htmlspecialchars($formData['designation']); ?>" placeholder="e.g., Manager, Owner">
                    </div>
                </div>

                <!-- Industry & Experience -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="industry_sector">Industry / Business Sector</label>
                        <select id="industry_sector" name="industry_sector">
                            <option value="">-- Select --</option>
                            <option value="Retail"         <?php echo ($formData['industry_sector'] == 'Retail')         ? 'selected' : ''; ?>>Retail</option>
                            <option value="Manufacturing"  <?php echo ($formData['industry_sector'] == 'Manufacturing')  ? 'selected' : ''; ?>>Manufacturing</option>
                            <option value="Logistics"      <?php echo ($formData['industry_sector'] == 'Logistics')      ? 'selected' : ''; ?>>Logistics</option>
                            <option value="Services"       <?php echo ($formData['industry_sector'] == 'Services')       ? 'selected' : ''; ?>>Services</option>
                            <option value="E-commerce"     <?php echo ($formData['industry_sector'] == 'E-commerce')     ? 'selected' : ''; ?>>E-commerce</option>
                            <option value="Education"      <?php echo ($formData['industry_sector'] == 'Education')      ? 'selected' : ''; ?>>Education</option>
                            <option value="Other"          <?php echo ($formData['industry_sector'] == 'Other')          ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="experience">Years of Business / Work Experience</label>
                        <select id="experience" name="experience">
                            <option value="">-- Select --</option>
                            <option value="Fresher"       <?php echo ($formData['experience'] == 'Fresher')       ? 'selected' : ''; ?>>Fresher</option>
                            <option value="Below 2 Years" <?php echo ($formData['experience'] == 'Below 2 Years') ? 'selected' : ''; ?>>Below 2 Years</option>
                            <option value="2-5 Years"     <?php echo ($formData['experience'] == '2-5 Years')     ? 'selected' : ''; ?>>2–5 Years</option>
                            <option value="5+ Years"      <?php echo ($formData['experience'] == '5+ Years')      ? 'selected' : ''; ?>>5+ Years</option>
                            <option value="10+ Years"     <?php echo ($formData['experience'] == '10+ Years')     ? 'selected' : ''; ?>>10+ Years</option>
                            <option value="15+ Years"     <?php echo ($formData['experience'] == '15+ Years')     ? 'selected' : ''; ?>>15+ Years</option>
                        </select>
                    </div>
                </div>

                <!-- Preferred Training Area -->
                <div class="form-group">
                    <label for="preferred_training">Select Your Preferred Training Area(s) <span class="required">*</span></label>
                    <select id="preferred_training" name="preferred_training" required>
                        <option value="">-- Select --</option>
                        <option value="Communication Skills"                                     <?php echo ($formData['preferred_training'] == 'Communication Skills')                                     ? 'selected' : ''; ?>>Communication Skills</option>
                        <option value="Customer Service & CRM"                                   <?php echo ($formData['preferred_training'] == 'Customer Service & CRM')                                   ? 'selected' : ''; ?>>Customer Service &amp; Customer Relationship Management</option>
                        <option value="Leadership & Team Management"                             <?php echo ($formData['preferred_training'] == 'Leadership & Team Management')                             ? 'selected' : ''; ?>>Leadership &amp; Team Management</option>
                        <option value="Workplace Productivity & Time Management"                 <?php echo ($formData['preferred_training'] == 'Workplace Productivity & Time Management')                 ? 'selected' : ''; ?>>Workplace Productivity &amp; Time Management</option>
                        <option value="Sales & Business Development"                             <?php echo ($formData['preferred_training'] == 'Sales & Business Development')                             ? 'selected' : ''; ?>>Sales &amp; Business Development</option>
                        <option value="Operation & Workplace Skills"                             <?php echo ($formData['preferred_training'] == 'Operation & Workplace Skills')                             ? 'selected' : ''; ?>>Operation &amp; Workplace Skills</option>
                        <option value="Business Growth & Entrepreneurship"                       <?php echo ($formData['preferred_training'] == 'Business Growth & Entrepreneurship')                       ? 'selected' : ''; ?>>Business Growth &amp; Entrepreneurship</option>
                        <option value="Others"                                                   <?php echo ($formData['preferred_training'] == 'Others')                                                   ? 'selected' : ''; ?>>Others</option>
                    </select>
                </div>

                <!-- Customized Training & Participants -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="customized_training">Interested in customized training for your business / team?</label>
                        <select id="customized_training" name="customized_training">
                            <option value="">-- Select --</option>
                            <option value="Yes" <?php echo ($formData['customized_training'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                            <option value="No"  <?php echo ($formData['customized_training'] == 'No')  ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="num_participants">Number of Participants (if team training)</label>
                        <select id="num_participants" name="num_participants">
                            <option value="">-- Select --</option>
                            <option value="Individual" <?php echo ($formData['num_participants'] == 'Individual') ? 'selected' : ''; ?>>Individual</option>
                            <option value="2-5"        <?php echo ($formData['num_participants'] == '2-5')        ? 'selected' : ''; ?>>2–5</option>
                            <option value="6-10"       <?php echo ($formData['num_participants'] == '6-10')       ? 'selected' : ''; ?>>6–10</option>
                            <option value="11-20"      <?php echo ($formData['num_participants'] == '11-20')      ? 'selected' : ''; ?>>11–20</option>
                            <option value="20+"        <?php echo ($formData['num_participants'] == '20+')        ? 'selected' : ''; ?>>20+</option>
                        </select>
                    </div>
                </div>

                <!-- Skills to Improve -->
                <div class="form-group">
                    <label for="skills_to_improve">What skills would you like to improve?</label>
                    <textarea id="skills_to_improve" name="skills_to_improve" rows="2" placeholder="Describe the skills you want to build..."><?php echo htmlspecialchars($formData['skills_to_improve']); ?></textarea>
                </div>

                <!-- Business Challenges -->
                <div class="form-group">
                    <label for="business_challenges">What are your key business or workplace challenges?</label>
                    <textarea id="business_challenges" name="business_challenges" rows="2" placeholder="Describe your current challenges..."><?php echo htmlspecialchars($formData['business_challenges']); ?></textarea>
                </div>

                <!-- Expected Outcome -->
                <div class="form-group">
                    <label for="expected_outcome">What do you expect to achieve through this training?</label>
                    <textarea id="expected_outcome" name="expected_outcome" rows="2" placeholder="Describe your goals..."><?php echo htmlspecialchars($formData['expected_outcome']); ?></textarea>
                </div>

                <!-- Source -->
                <div class="form-group">
                    <label for="source">How did you hear about Never Give Up World Foundation?</label>
                    <select id="source" name="source">
                        <option value="">-- Select --</option>
                        <option value="Website"            <?php echo ($formData['source'] == 'Website')            ? 'selected' : ''; ?>>Website</option>
                        <option value="Facebook"           <?php echo ($formData['source'] == 'Facebook')           ? 'selected' : ''; ?>>Facebook</option>
                        <option value="Instagram"          <?php echo ($formData['source'] == 'Instagram')          ? 'selected' : ''; ?>>Instagram</option>
                        <option value="LinkedIn"           <?php echo ($formData['source'] == 'LinkedIn')           ? 'selected' : ''; ?>>LinkedIn</option>
                        <option value="WhatsApp"           <?php echo ($formData['source'] == 'WhatsApp')           ? 'selected' : ''; ?>>WhatsApp</option>
                        <option value="Friends / Colleagues" <?php echo ($formData['source'] == 'Friends / Colleagues') ? 'selected' : ''; ?>>Friends / Colleagues</option>
                        <option value="Other"              <?php echo ($formData['source'] == 'Other')              ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <!-- Declaration -->
                <div class="form-group declaration-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="declaration" required>
                        <span>I confirm that the information provided in this registration form is accurate and complete. I consent to being contacted by Never Give Up World Foundation regarding the Capacity Building &amp; Skills Training Program, training schedules, and related opportunities.</span>
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
        const modal = document.getElementById('capacityModal');
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