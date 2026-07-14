<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");
?>

<link rel="stylesheet" href="../assets/css/info-styles.css">
<link rel="stylesheet" href="../assets/css/animations.css">

<section id="about" class="ln-info-about-layout reveal">
    <div class="ln-info-about-header">
        <p class="ln-info-tag">EXPLORE</p>
        <h1 class="ln-info-title">About The Literary Nook</h1>
    </div>
    
    <div class="ln-info-narrative-strip">
        <div class="ln-info-narrative-inner">
            <p>Welcome to <strong>The Literary Nook</strong>, your ultimate digital sanctuary for stories, literature, and knowledge. Curated with a deep passion for the written word, our platform serves as a modern harbor for book lovers and avid readers alike.</p>
            <p>Inspired by rich, immersive digital interfaces, we bridge the gap between classic physical tracking and seamless web cataloging to bring your collection straight to your screen.</p>
        </div>
    </div>
</section>

<section id="contact" class="ln-info-contact-section reveal">
    <div class="ln-info-contact-container">
        <div class="ln-info-contact-left">
            <p class="ln-info-tag">GET IN TOUCH</p>
            <h2 class="ln-info-title">Contact Information</h2>
            <p class="ln-info-contact-lead">Have questions, technical feedback, or programmatic partnership opportunities? Our support network is monitoring incoming logs to assist you.</p>
            
            <div class="ln-info-contact-matrix">
                <div class="ln-info-matrix-row">
                    <span class="ln-info-matrix-label">Email Support:</span>
                    <span class="ln-info-matrix-value">support@literarynook.local</span>
                </div>
                <div class="ln-info-matrix-row">
                    <span class="ln-info-matrix-label">Laboratory Location:</span>
                    <span class="ln-info-matrix-value">Manila, Metro Manila, Philippines</span>
                </div>
                <div class="ln-info-matrix-row">
                    <span class="ln-info-matrix-row">
                        <span class="ln-info-matrix-label">Operation Hours:</span>
                        <span class="ln-info-matrix-value">Monday – Friday, 09:00 - 17:00 PST</span>
                    </span>
                </div>
            </div>
            
            <div class="ln-info-social-row">
                <a href="#" class="ln-info-social-btn">
                    <img src="../assets/css/images/facebookLogo.png" alt="Facebook" class="social-icon-img">
                </a>
                <a href="#" class="ln-info-social-btn">
                    <img src="../assets/css/images/instagramLogo.png" alt="Instagram" class="social-icon-img">
                </a>
                <a href="#" class="ln-info-social-btn">
                    <img src="../assets/css/images/Xlogo.png" class="ln-info-social-img" alt="X">
                </a>
            </div>
        </div>
        <div class="ln-info-contact-right">
            <div class="ln-info-contact-image-frame"></div>
        </div>
    </div>
</section>

<section id="privacy" class="ln-info-privacy-section reveal">
    <div class="ln-info-privacy-container">
        <div class="ln-info-privacy-left">
            <div class="ln-info-privacy-image-frame"></div>
        </div>
        
        <div class="ln-info-privacy-right">
            <p class="ln-info-tag">LEGAL COMPLIANCE</p>
            <h2 class="ln-info-title">Privacy Policy</h2>
            
            <div class="ln-info-privacy-text-wrapper">
                <p class="ln-info-privacy-date">Last Updated: July 2026</p>
                <p class="ln-info-privacy-lead">At The Literary Nook, we take your data architecture security seriously. This document outlines how data handles across our local system nodes:</p>
                
                <div class="ln-info-legal-clause">
                    <h3>1. Information We Collect</h3>
                    <p>We log profile registration sets (usernames, credentials, role structures) to accurately populate authentication parameters and custom user wishlists.</p>
                </div>
                
                <div class="ln-info-legal-clause">
                    <h3>2. Data Encryption</h3>
                    <p>Sensitive parameters are systematically processed via structural framework cryptography before hitting active database storage engines.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="terms" class="ln-info-terms-section reveal">
    <div class="ln-info-terms-container">
        <div class="ln-info-terms-left-flank ln-info-terms-flank"></div>
        
        <div class="ln-info-terms-center">
            <p class="ln-info-tag">LEGAL AGREEMENT</p>
            <h2 class="ln-info-title">Terms of Service</h2>
            
            <div class="ln-info-terms-card">
                <p class="ln-info-terms-date">Last Updated: July 2026</p>
                <p class="ln-info-terms-lead">By accessing or utilizing our system inventory environments, you explicitly agree to align with the terms specified herein:</p>
                
                <div class="ln-info-terms-clause">
                    <h3>1. Account Usage Verification</h3>
                    <p>Users are responsible for safeguarding authorization tokens, session assignments, and login cookies. Standard accounts attempting to access restricted administrative logic strings face strict account termination parameters.</p>
                </div>
                
                <div class="ln-info-terms-clause">
                    <h3>2. Limitation of Liability</h3>
                    <p>This web application is delivered "as-is" for tracking purposes under developmental educational project guidelines.</p>
                </div>
            </div>
        </div>
        
        <div class="ln-info-terms-right-flank ln-info-terms-flank"></div>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const scrollIndicator = document.querySelector('.blueprint-scroll-indicator');
    
    if (scrollIndicator) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 80) {
                scrollIndicator.classList.add('fade-out');
            } else {
                scrollIndicator.classList.remove('fade-out');
            }
        });
    }
});
</script>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>