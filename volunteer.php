<?php
// volunteer.php - Volunteer Registration Page

// Define page-specific CSS
$extraCSS = [
    'assets/css/volunteer.css'
];

// Include DB connection
require_once 'includes/db.php';

// Initialize variables
$successMessage = '';
$errorMessage = '';
$formData = [
    'full_name' => '',
    'email' => '',
    'phone' => '',
    'city' => '',
    'area_of_interest' => '',
    'availability' => '',
    'message' => ''
];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitize and assign inputs
    $formData['full_name']       = trim($_POST['full_name'] ?? '');
    $formData['email']           = trim($_POST['email'] ?? '');
    $formData['phone']           = trim($_POST['phone'] ?? '');
    $formData['city']            = trim($_POST['city'] ?? '');
    $formData['area_of_interest']= trim($_POST['area_of_interest'] ?? '');
    $formData['availability']    = trim($_POST['availability'] ?? '');
    $formData['message']         = trim($_POST['message'] ?? '');

    // Basic Validation
    $errors = [];
    if (empty($formData['full_name'])) $errors[] = "Full Name is required.";
    if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "A valid Email is required.";
    if (empty($formData['phone'])) $errors[] = "Phone Number is required.";
    if (empty($formData['city'])) $errors[] = "City is required.";
    if (empty($formData['area_of_interest'])) $errors[] = "Please select an Area of Interest.";
    if (empty($formData['availability'])) $errors[] = "Please select your Availability.";

    if (empty($errors)) {
        try {
            // Insert into database
            $sql = "INSERT INTO volunteers (full_name, email, phone, city, area_of_interest, availability, message) 
                    VALUES (:full_name, :email, :phone, :city, :area_of_interest, :availability, :message)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':full_name'        => $formData['full_name'],
                ':email'            => $formData['email'],
                ':phone'            => $formData['phone'],
                ':city'             => $formData['city'],
                ':area_of_interest' => $formData['area_of_interest'],
                ':availability'     => $formData['availability'],
                ':message'          => $formData['message']
            ]);

            $successMessage = "Thank you for registering as a volunteer! We will contact you soon.";
            
            // Clear form data on success
            $formData = array_map(fn() => '', $formData);

        } catch (PDOException $e) {
            $errorMessage = "Something went wrong. Please try again later. (Error: " . $e->getMessage() . ")";
        }
    } else {
        $errorMessage = implode("<br>", $errors);
    }
}

include 'includes/header.php';
?>

<!-- VOLUNTEER HERO SECTION -->
<section class="volunteer-hero-section">
    <div class="container">
        <h1>Become a Volunteer</h1>
        <p>Share your time, knowledge and expertise with communities. Your involvement creates real impact.</p>
    </div>
</section>

<!-- VOLUNTEER FORM SECTION -->
<section class="volunteer-form-section">
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
            <h2>Volunteer Registration Form</h2>
            <p class="form-intro">Please fill in the details below and we will get in touch with you.</p>

            <form action="volunteer.php" method="POST" class="volunteer-form">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Full Name <span class="required">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($formData['full_name']); ?>" required placeholder="Enter your full name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" required placeholder="Enter your email">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($formData['phone']); ?>" required placeholder="Enter your phone number">
                    </div>
                    <div class="form-group">
                        <label for="city">City <span class="required">*</span></label>
                        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($formData['city']); ?>" required placeholder="Enter your city">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="area_of_interest">Area of Interest <span class="required">*</span></label>
                        <select id="area_of_interest" name="area_of_interest" required>
                            <option value="">-- Select an Area --</option>
                            <option value="Skill Development" <?php echo ($formData['area_of_interest'] == 'Skill Development') ? 'selected' : ''; ?>>Skill Development</option>
                            <option value="Capacity Building" <?php echo ($formData['area_of_interest'] == 'Capacity Building') ? 'selected' : ''; ?>>Capacity Building</option>
                            <option value="Child Education" <?php echo ($formData['area_of_interest'] == 'Child Education') ? 'selected' : ''; ?>>Child Education</option>
                            <option value="Happiness & Positive Psychology" <?php echo ($formData['area_of_interest'] == 'Happiness & Positive Psychology') ? 'selected' : ''; ?>>Happiness & Positive Psychology</option>
                            <option value="General Support" <?php echo ($formData['area_of_interest'] == 'General Support') ? 'selected' : ''; ?>>General Support</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="availability">Availability <span class="required">*</span></label>
                        <select id="availability" name="availability" required>
                            <option value="">-- Select Availability --</option>
                            <option value="Weekdays" <?php echo ($formData['availability'] == 'Weekdays') ? 'selected' : ''; ?>>Weekdays</option>
                            <option value="Weekends" <?php echo ($formData['availability'] == 'Weekends') ? 'selected' : ''; ?>>Weekends</option>
                            <option value="Evenings" <?php echo ($formData['availability'] == 'Evenings') ? 'selected' : ''; ?>>Evenings</option>
                            <option value="Flexible" <?php echo ($formData['availability'] == 'Flexible') ? 'selected' : ''; ?>>Flexible</option>
                        </select>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="message">Message / Why do you want to volunteer?</label>
                    <textarea id="message" name="message" rows="5" placeholder="Tell us a bit about yourself..."><?php echo htmlspecialchars($formData['message']); ?></textarea>
                </div>

                <div class="form-group full-width">
                    <button type="submit" class="btn btn-primary btn-submit">
                        Submit Application <i class="fas fa-paper-plane"></i>
                    </button>
                </div>

            </form>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>