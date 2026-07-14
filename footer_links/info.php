<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");
?>

<link rel="stylesheet" href="../assets/css/info-styles.css">
<!-- ===========================================
     ABOUT SECTION (CINEMATIC FULL-WIDTH LAYOUT)
=========================================== -->
<section id="about" class="project-about-layout reveal">
    
    <!-- Top Header Area -->
    <div class="about-header-zone">
        <p class="section-tag">EXPLORE</p>
        <h2 class="section-title">About The Literary Nook</h2>
    </div>
    
    <!-- Full-bleed background strip that stretches 100% horizontally -->
    <div class="info-content-narrative-strip">
        <div class="narrative-inner-content">
            <p>Welcome to <strong>The Literary Nook</strong>, your ultimate digital sanctuary for stories, literature, and knowledge. Curated with a deep passion for the written word, our platform serves as a modern harbor for book lovers and avid readers alike.</p>
            <p>Inspired by rich, immersive digital interfaces, we bridge the gap between classic physical tracking and seamless web cataloging to bring your collection straight to your screen.</p>
        </div>
    </div>

    <!-- Bottom Indicator Area -->
    <div class="about-footer-zone">
        <div class="blueprint-scroll-indicator">
            <div class="scroll-arrow">↓</div>
            <span class="scroll-text">SCROLL</span>
        </div>
    </div>
    
</section>

<div class="section-divider"></div>

<!-- ===========================================
     CONTACT SECTION
=========================================== -->
<section id="contact" class="why-section reveal contact-split-section">
    <div class="contact-split-container">
        
        <div class="contact-left-panel">
            <p class="section-tag">GET IN TOUCH</p>
            <h2 class="section-title">Contact Information</h2>
            
            <p class="contact-narrative-lead">
                Have questions, technical feedback, or programmatic partnership opportunities? Our support network is monitoring incoming logs to assist you.
            </p>
            
            <div class="contact-details-matrix">
                <div class="matrix-row">
                    <span class="matrix-label">Email Support:</span>
                    <span class="matrix-value">support@literarynook.local</span>
                </div>
                <div class="matrix-row">
                    <span class="matrix-label">Laboratory Location:</span>
                    <span class="matrix-value">Manila, Metro Manila, Philippines</span>
                </div>
                <div class="matrix-row">
                    <span class="matrix-label">Operation Hours:</span>
                    <span class="matrix-value">Monday – Friday, 09:00 - 17:00 PST</span>
                </div>
            </div>

            <div class="contact-social-row">
                <a href="#" class="social-icon-placeholder" aria-label="Facebook Link String">
                    <img src="../assets/css/images/facebookLogo.png" alt="Facebook" class="social-icon-img">
                </a>
                <a href="#" class="social-icon-placeholder" aria-label="Instagram Link String">
                    <img src="../assets/css/images/instagramLogo.png" alt="Instagram" class="social-icon-img">
                </a>
                <a href="#" class="social-icon-placeholder" aria-label="X Link String">
                    <img src="../assets/css/images/xLogo.png" alt="X" class="social-icon-img">
                </a>
            </div>
        </div>

        <div class="contact-right-panel">
            <div class="hero-image-placeholder">
                <div class="placeholder-overlay-text"></div>
            </div>
        </div>

    </div>
</section>

<section id="privacy" class="why-section reveal privacy-split-section">
    <div class="privacy-split-container">
        
        <div class="privacy-left-panel">
            <div class="privacy-image-placeholder">
            </div>
        </div>

        <div class="privacy-right-panel">
            <p class="section-tag">LEGAL COMPLIANCE</p>
            <h2 class="section-title">Privacy Policy</h2>
            
            <div class="privacy-text-wrapper">
                <p class="privacy-date">Last Updated: July 2026</p>
                <p class="privacy-narrative-lead">
                    At The Literary Nook, we take your data architecture security seriously. This document outlines how data handles across our local system nodes:
                </p>
                
                <div class="legal-clause-block">
                    <h3>1. Information We Collect</h3>
                    <p>We log profile registration sets (usernames, hashed credentials, role structures) to accurately populate authentication parameters and custom user wishlists.</p>
                </div>
                
                <div class="legal-clause-block">
                    <h3>2. Data Encryption</h3>
                    <p>Sensitive parameters are systematically processed via structural framework cryptography before hitting active database storage engines.</p>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ===========================================
     TERMS OF SERVICE SECTION
=========================================== -->
<!-- ===========================================
     TERMS OF SERVICE SECTION (BOOKCASE FLANKED LAYOUT)
=========================================== -->
<section id="terms" class="why-section reveal terms-bookcase-layout">
    <div class="terms-grid-container">
        
        <!-- LEFT PANEL: BOOK STACK FLANK (Repeated Background) -->
        <div class="terms-flank-panel left-flank"></div>
        
        <!-- MIDDLE PANEL: CENTRAL LEGAL DOCUMENT SYSTEM -->
        <div class="terms-center-content">
            <p class="section-tag">LEGAL AGREEMENT</p>
            <h2 class="section-title">Terms of Service</h2>
            
            <!-- Floating Inner Text Card Frame -->
            <div class="terms-document-card">
                <p class="terms-card-date">Last Updated: July 2026</p>
                
                <p class="terms-card-narrative">
                    By accessing or utilizing our system inventory environments, you explicitly agree to align with the terms specified herein:
                </p>
                
                <div class="terms-card-clause">
                    <h3>1. Account Usage Verification</h3>
                    <p>Users are responsible for safeguarding authorization tokens, session assignments, and login cookies. Standard accounts attempting to access restricted administrative logic strings face strict account termination parameters.</p>
                </div>
                
                <div class="terms-card-clause">
                    <h3>2. Limitation of Liability</h3>
                    <p>This web application is delivered "as-is" for tracking purposes under developmental educational project guidelines.</p>
                </div>
            </div>
        </div>
        
        <!-- RIGHT PANEL: BOOK STACK FLANK (Repeated Background) -->
        <div class="terms-flank-panel right-flank"></div>
        
    </div>
</section>
<!-- THE FIX: Place the script and footer here, completely outside of any sections -->

<!-- SCRIPT TO FADE OUT ARROW ON WINDOW SCROLL -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const scrollIndicator = document.querySelector('.blueprint-scroll-indicator');
    
    if (scrollIndicator) {
        window.addEventListener('scroll', function() {
            // If the browser scrolls past 80 pixels downward, fade it away
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