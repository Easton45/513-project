<?php
function myshop_recruit_form() {
    $message = '';
    
    // Handle form submission
    if (isset($_POST['submit_application'])) {
        // Simple Validation
        if (empty($_POST['applicant_name']) || empty($_POST['applicant_contact'])) {
             $message = '<div class="sfood-alert sfood-alert-danger"><i class="fas fa-exclamation-circle"></i> Please provide your full name and contact information.</div>';
        } 
        elseif (!empty($_FILES['cv_upload']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            // Allowed file types
            $overrides = ['test_form' => false, 'mimes' => ['pdf' => 'application/pdf', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'doc' => 'application/msword']];
            
            $uploaded = wp_handle_upload($_FILES['cv_upload'], $overrides);
            
            if ($uploaded && !isset($uploaded['error'])) {
                // In a real project, send email to admin or save to DB
                // Only showing success message for demo
                $message = '<div class="sfood-alert sfood-alert-success">
                                <h4><i class="fas fa-check-circle"></i> Application Submitted!</h4>
                                <p>Thank you for applying for the <strong>'.esc_html($_POST['job']).'</strong> position.<br>If we are interested in your profile, we will contact you via <strong>'.esc_html($_POST['applicant_contact']).'</strong>.</p>
                            </div>';
            } else {
                $message = '<div class="sfood-alert sfood-alert-danger">Upload failed: ' . $uploaded['error'] . '</div>';
            }
        } else {
            $message = '<div class="sfood-alert sfood-alert-danger">Please upload your resume file.</div>';
        }
    }

    ob_start();
    ?>
    <div class="sfood-container">
        <div class="recruit-header text-center">
            <h1>Join the SFOOD Family</h1>
            <p class="subtitle">Join us in delivering authentic ethnic delicacies to every diner</p>
        </div>

        <div class="job-positions-grid">
            <div class="job-card">
                <div class="icon-box"><i class="fas fa-store"></i></div>
                <h3>Store Manager Trainee</h3>
                <p>Responsible for daily store operations and service quality. We need your leadership and passion for the catering industry.</p>
            </div>
            <div class="job-card">
                <div class="icon-box"><i class="fas fa-utensils"></i></div>
                <h3>Chinese Cuisine Chef</h3>
                <p>Proficient in Sichuan, Cantonese, or Dim Sum preparation. We look for artisans who are strict with ingredients and dedicated to flavor.</p>
            </div>
            <div class="job-card">
                <div class="icon-box"><i class="fas fa-motorcycle"></i></div>
                <h3>Delivery Specialist</h3>
                <p>Familiar with city routes, delivering food safely and on time. We need you to be punctual, polite, and responsible.</p>
            </div>
        </div>

        <div class="recruit-form-wrapper">
            <div class="form-header">
                <h3><i class="fas fa-file-signature"></i> Online Application</h3>
                <p>Please fill in the information below and upload your resume. We look forward to having you!</p>
            </div>
            
            <?php echo $message; ?>

            <form method="post" enctype="multipart/form-data" class="sfood-recruit-form">
                <div class="form-group">
                    <label>Your Name <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="applicant_name" required placeholder="Enter your full name">
                    </div>
                </div>

                <div class="form-group">
                    <label>Contact Phone/Email <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-phone-alt"></i>
                        <input type="text" name="applicant_contact" required placeholder="Best way to contact you">
                    </div>
                </div>

                <div class="form-group">
                    <label>Position Applied For <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-briefcase"></i>
                        <select name="job">
                            <option value="Store Manager">Store Manager Trainee</option>
                            <option value="Chef">Chinese Cuisine Chef</option>
                            <option value="Delivery">Delivery Specialist</option>
                            <option value="Part-time">Part-time Staff</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Upload Resume (PDF/Word) <span class="required">*</span></label>
                    <div class="file-upload-box">
                        <input type="file" name="cv_upload" id="cv_upload" required accept=".pdf,.doc,.docx">
                        <small class="form-text">File size limit: 2MB</small>
                    </div>
                </div>

                <button type="submit" name="submit_application" class="btn-submit-recruit">
                    Submit Application <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('my_recruit_form', 'myshop_recruit_form');
?>