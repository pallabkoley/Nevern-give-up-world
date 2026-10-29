<?php
// support.php - Support / Donate Page

// Define page-specific CSS
$extraCSS = [
    'assets/css/support.css'
];

// Include DB connection
require_once 'includes/db.php';

// Initialize variables
$successMessage = '';
$errorMessage = '';
$formData = [
    'donor_name'     => '',
    'email'          => '',
    'phone'          => '',
    'amount'         => '',
    'payment_method' => '',
    'utr_number'     => '',
    'donation_date'  => date('Y-m-d'),
    'message'        => ''
];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitize inputs
    foreach ($formData as $key => $value) {
        $formData[$key] = trim($_POST[$key] ?? '');
    }

    // Validation
    $errors = [];
    if (empty($formData['donor_name']))                                     $errors[] = "Donor Name is required.";
    if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "A valid Email Address is required.";
    if (empty($formData['phone']))                                          $errors[] = "Phone Number is required.";
    if (empty($formData['amount']) || !is_numeric($formData['amount']) || $formData['amount'] <= 0) $errors[] = "A valid Donation Amount is required.";
    if (empty($formData['payment_method']))                                 $errors[] = "Please select a Payment Method.";
    if (empty($formData['utr_number']))                                     $errors[] = "UTR / Transaction Reference Number is required.";
    if (empty($formData['donation_date']))                                  $errors[] = "Donation Date is required.";

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO donations 
                    (donor_name, email, phone, amount, payment_method, utr_number, donation_date, message) 
                    VALUES 
                    (:donor_name, :email, :phone, :amount, :payment_method, :utr_number, :donation_date, :message)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':donor_name'     => $formData['donor_name'],
                ':email'          => $formData['email'],
                ':phone'          => $formData['phone'],
                ':amount'         => $formData['amount'],
                ':payment_method' => $formData['payment_method'],
                ':utr_number'     => $formData['utr_number'],
                ':donation_date'  => $formData['donation_date'],
                ':message'        => $formData['message']
            ]);

            $successMessage = "Thank you for your generous donation! Your contribution will help shape a child's future. We will send you a receipt shortly.";
            
            // Clear form on success
            $formData = array_map(fn() => '', $formData);
            $formData['donation_date'] = date('Y-m-d');

        } catch (PDOException $e) {
            $errorMessage = "Something went wrong. Please try again later.";
        }
    } else {
        $errorMessage = implode("<br>", $errors);
    }
}

include 'includes/header.php';
?>

<!-- SUPPORT HERO SECTION -->
<section class="support-hero-section">
    <div class="container">
        <h1>Donate to Child Education</h1>
        <p>Your donation can shape a child's future. Every contribution helps break barriers and invest in their future.</p>
    </div>
</section>

<!-- SUPPORT CONTENT SECTION -->
<section class="support-content-section">
    <div class="container support-grid">
        
        <!-- Left: Image -->
        <div class="support-image-wrapper">
            <img src="assets/images/donation.jpg" alt="Donate to Child Education - Never Give Up World Foundation">
        </div>

        <!-- Right: Form -->
        <div class="support-form-wrapper">
            
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

            <h2>Donation Details Submission</h2>
            <p class="form-intro">If you have already made a donation via UPI or Bank Transfer, please fill in the details below so we can send you your 80G tax benefit receipt.</p>

            <form action="support.php" method="POST" class="donation-form">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="donor_name">Full Name <span class="required">*</span></label>
                        <input type="text" id="donor_name" name="donor_name" value="<?php echo htmlspecialchars($formData['donor_name']); ?>" required placeholder="Enter your full name">
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
                        <label for="amount">Donation Amount (INR) <span class="required">*</span></label>
                        <input type="number" id="amount" name="amount" value="<?php echo htmlspecialchars($formData['amount']); ?>" required placeholder="e.g. 1000" min="1" step="0.01">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="payment_method">Payment Method <span class="required">*</span></label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="">-- Select Method --</option>
                            <option value="UPI" <?php echo ($formData['payment_method'] == 'UPI') ? 'selected' : ''; ?>>UPI</option>
                            <option value="Bank Transfer" <?php echo ($formData['payment_method'] == 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                            <option value="Credit/Debit Card" <?php echo ($formData['payment_method'] == 'Credit/Debit Card') ? 'selected' : ''; ?>>Credit/Debit Card</option>
                            <option value="Net Banking" <?php echo ($formData['payment_method'] == 'Net Banking') ? 'selected' : ''; ?>>Net Banking</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="donation_date">Donation Date <span class="required">*</span></label>
                        <input type="date" id="donation_date" name="donation_date" value="<?php echo htmlspecialchars($formData['donation_date']); ?>" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="utr_number">UTR / Transaction Reference Number <span class="required">*</span></label>
                    <input type="text" id="utr_number" name="utr_number" value="<?php echo htmlspecialchars($formData['utr_number']); ?>" required placeholder="Enter UTR / Reference Number from your bank">
                </div>

                <div class="form-group full-width">
                    <label for="message">Message (Optional)</label>
                    <textarea id="message" name="message" rows="3" placeholder="Any specific cause or message..."><?php echo htmlspecialchars($formData['message']); ?></textarea>
                </div>

                <div class="form-group full-width">
                    <button type="submit" class="btn btn-primary btn-submit">
                        Submit Donation Details <i class="fas fa-paper-plane"></i>
                    </button>
                </div>

            </form>
        </div>

    </div>
</section>

<!-- DONATION PROVIDES SECTION -->
<section class="donation-provides-section">
    <div class="container">
        <h2 class="section-title">Your Donation Will Help Us Provide</h2>
        <div class="provides-grid">
            <div class="provide-card">
                <i class="fas fa-school"></i>
                <h3>School Supplies &amp; Uniforms</h3>
            </div>
            <div class="provide-card">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>Qualified Teachers &amp; Educational Resources</h3>
            </div>
            <div class="provide-card">
                <i class="fas fa-apple-alt"></i>
                <h3>Nutritional Support</h3>
            </div>
        </div>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="support-cta-section">
    <div class="container">
        <h2>For More Details Please Call: <a href="tel:9883086911">9883086911</a></h2>
        <p>Your contribution is eligible for <strong>Section 80G Tax Benefit</strong>.</p>
    </div>
</section>

<?php
include 'includes/footer.php';
?>