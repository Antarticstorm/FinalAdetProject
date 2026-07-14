<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");
?>

<link rel="stylesheet" href="/CCS0043/FinalAdetProject/assets/css/info-styles.css">

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
<section id="contact" class="why-section reveal info-section-wrapper">
    <div class="container">
        <p class="section-tag">GET IN TOUCH</p>
        <h2 class="section-title">Contact Information</h2>
        <div class="info-text-block">
            <p>Have questions, technical feedback, or programmatic partnership opportunities? Our support network is monitoring incoming logs to assist you.</p>
            <div style="border-top: 1px dashed var(--border, #334155); padding-top: 16px; margin-top: 16px;">
                <p style="margin-bottom: 8px;"><strong>Email Support:</strong> support@literarynook.local</p>
                <p style="margin-bottom: 8px;"><strong>Systems Laboratory Location:</strong> Manila, Metro Manila, Philippines</p>
                <p><strong>Operation Hours:</strong> Monday – Friday, 09:00 - 17:00 PST</p>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- ===========================================
     PRIVACY POLICY SECTION
=========================================== -->
<section id="privacy" class="why-section reveal info-section-wrapper">
    <div class="container">
        <p class="section-tag">LEGAL COMPLIANCE</p>
        <h2 class="section-title">Privacy Policy</h2>
        <div class="info-text-block">
            <p style="font-size: 0.85rem; color: var(--text-muted, #94a3b8); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px;">Last Updated: July 2026</p>
            <p>At The Literary Nook, we take your data architecture security seriously. This document outlines how data handles across our local system nodes:</p>
            <h3>1. Information We Collect</h3>
            <p>We log profile registration sets (usernames, hashed credentials, role structures) to accurately populate authentication parameters and custom user wishlists.</p>
            <h3>2. Data Encryption</h3>
            <p>Sensitive parameters are systematically processed via structural framework cryptography before hitting active database storage engines.</p>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- ===========================================
     TERMS OF SERVICE SECTION
=========================================== -->
<section id="terms" class="why-section reveal info-section-wrapper">
    <div class="container">
        <p class="section-tag">LEGAL AGREEMENT</p>
        <h2 class="section-title">Terms of Service</h2>
        <div class="info-text-block">
            <p style="font-size: 0.85rem; color: var(--text-muted, #94a3b8); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px;">Last Updated: July 2026</p>
            <p>By accessing or utilizing our system inventory environments, you explicitly agree to align with the terms specified herein:</p>
            <h3>1. Account Usage Verification</h3>
            <p>Users are responsible for safeguarding authorization tokens, session assignments, and login cookies. Standard accounts attempting to access restricted administrative logic strings face strict account termination parameters.</p>
            <h3>2. Limitation of Liability</h3>
            <p>This web application is delivered "as-is" for tracking purposes under developmental educational project guidelines.</p>
        </div>
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