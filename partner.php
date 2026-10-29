<?php
// partner.php - Partner With Us Registration Page

// Define page-specific CSS
$extraCSS = [
    'assets/css/partner.css'
];

// Include DB connection
require_once 'includes/db.php';

// Initialize variables
$successMessage = '';
$errorMessage = '';
$formData = [
    'organization_name'   => '',
    'contact_person'      => '',
    'email'               => '',
    'phone'               => '',
    'website'             => '',
    'city'                => '',
    'partner_type'        => '',
    'partnership_interest'=> '',
    'message'             => ''
];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitize inputs
    foreach ($formData as $key => $value) {
        $formData[$key] = trim($_POST[$key] ?? '');
    }

    // Validation
    $errors = [];
    if (empty($formData['organization_name']))    $errors[] = "Organization / Company Name is required.";
    if (empty($formData['contact_person']))       $errors[] = "Contact Person Name is required.";
    if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "A valid Email Address is required.";
    if (empty($formData['phone']))                $errors[] = "Phone Number is required.";
    if (empty($formData['city']))                 $errors[] = "City is required.";
    if (empty($formData['partner_type']))         $errors[] = "Please select a Partner Type.";
    if (empty($formData['partnership_interest'])) $errors[] = "Please select an Area of Partnership Interest.";

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO partners 
                    (organization_name, contact_person, email, phone, website, city, partner_type, partnership_interest, message) 
                    VALUES 
                    (:organization_name, :contact_person, :email, :phone, :website, :city, :partner_type, :partnership_interest, :message)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':organization_name'    => $formData['organization_name'],
                ':contact_person'       => $formData['contact_person'],
                ':email'                => $formData['email'],
                ':phone'                => $formData['phone'],
                ':website'              => $formData['website'],
                ':city'                 => $formData['city'],
                ':partner_type'         => $formData['partner_type'],
                ':partnership_interest' => $formData['partnership_interest'],
                ':message'              => $formData['message']
            ]);

            $successMessage = "Thank you for your interest in partnering with us! Our team will reach out to you shortly.";
            
            // Clear form on success
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

<!-- PARTNER HERO SECTION -->
<section class="partner-form-hero-section">
    <div class="container">
        <h1>Partner With Us</h1>
        <p>Together, We Can Create Greater Impact. Fill out the form below and let's build something meaningful together.</p>
    </div>
</section>

<!-- PARTNER FORM SECTION -->
<section class="partner-form-section">
    <div class="container form-container">
        
        <!-- Success Message -->
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <div class="form-wrapper">
            <h2>Partnership Enquiry Form</h2>
            <p class="form-intro">Please provide details about your organization and how you would like to partner with us.</p>

            <form action="partner.php" method="POST" class="partner-form">
                
                <!-- Organization Details -->
                <h3 class="form-section-title">Organization Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="organization_name">Organization / Company Name <span class="required">*</span></label>
                        <input type="text" id="organization_name" name="organization_name" value="<?php echo htmlspecialchars($formData['organization_name']); ?>" required placeholder="Enter organization name">
                    </div>
                    <div class="form-group">
                        <label for="website">Website (Optional)</label>
                        <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($formData['website']); ?>" placeholder="https://example.com">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City <span class="required">*</span></label>
                        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($formData['city']); ?>" required placeholder="Enter city">
                    </div>
                    <div class="form-group">
                        <label for="partner_type">Partner Type <span class="required">*</span></label>
                        <select id="partner_type" name="partner_type" required>
                            <option value="">-- Select Partner Type --</option>
                            <option value="Corporate / CSR" <?php echo ($formData['partner_type'] == 'Corporate / CSR') ? 'selected' : ''; ?>>Corporate / CSR</option>
                            <option value="Small Business" <?php echo ($formData['partner_type'] == 'Small Business') ? 'selected' : ''; ?>>Small Business</option>
                            <option value="Educational Institution" <?php echo ($formData['partner_type'] == 'Educational Institution') ? 'selected' : ''; ?>>Educational Institution</option>
                            <option value="Industry Partner" <?php echo ($formData['partner_type'] == 'Industry Partner') ? 'selected' : ''; ?>>Industry Partner</option>
                            <option value="NGO / Non-Profit" <?php echo ($formData['partner_type'] == 'NGO / Non-Profit') ? 'selected' : ''; ?>>NGO / Non-Profit</option>
                            <option value="Individual Supporter" <?php echo ($formData['partner_type'] == 'Individual Supporter') ? 'selected' : ''; ?>>Individual Supporter</option>
                        </select>
                    </div>
                </div>

                <!-- Contact Details -->
                <h3 class="form-section-title">Contact Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_person">Contact Person Name <span class="required">*</span></label>
                        <input type="text" id="contact_person" name="contact_person" value="<?php echo htmlspecialchars($formData['contact_person']); ?>" required placeholder="Enter contact person name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" required placeholder="Enter email address">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($formData['phone']); ?>" required placeholder="Enter phone number">
                    </div>
                    <div class="form-group">
                        <label for="partnership_interest">Area of Partnership Interest <span class="required">*</span></label>
                        <select id="partnership_interest" name="partnership_interest" required>
                            <option value="">-- Select Interest --</option>
                            <option value="CSR Skill Development" <?php echo ($formData['partnership_interest'] == 'CSR Skill Development') ? 'selected' : ''; ?>>CSR Skill Development</option>
                            <option value="Industry-Oriented Training" <?php echo ($formData['partnership_interest'] == 'Industry-Oriented Training') ? 'selected' : ''; ?>>Industry-Oriented Training</option>
                            <option value="Employee Soft-Skill Development" <?php echo ($formData['partnership_interest'] == 'Employee Soft-Skill Development') ? 'selected' : ''; ?>>Employee Soft-Skill Development</option>
                            <option value="Small Business Capacity Building" <?php echo ($formData['partnership_interest'] == 'Small Business Capacity Building') ? 'selected' : ''; ?>>Small Business Capacity Building</option>
                            <option value="Youth Employability" <?php echo ($formData['partnership_interest'] == 'Youth Employability') ? 'selected' : ''; ?>>Youth Employability</option>
                            <option value="Education Support" <?php echo ($formData['partnership_interest'] == 'Education Support') ? 'selected' : ''; ?>>Education Support</option>
                            <option value="Community Well-being" <?php echo ($formData['partnership_interest'] == 'Community Well-being') ? 'selected' : ''; ?>>Community Well-being</option>
                            <option value="Internship / Job Opportunities" <?php echo ($formData['partnership_interest'] == 'Internship / Job Opportunities') ? 'selected' : ''; ?>>Internship / Job Opportunities</option>
                            <option value="Other" <?php echo ($formData['partnership_interest'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="message">Message / Additional Details</label>
                    <textarea id="message" name="message" rows="5" placeholder="Tell us more about how you'd like to partner with us..."><?php echo htmlspecialchars($formData['message']); ?></textarea>
                </div>

                <div class="form-group full-width">
                    <button type="submit" class="btn btn-primary btn-submit">
                        Submit Partnership Enquiry <i class="fas fa-paper-plane"></i>
                    </button>
                </div>

            </form>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>