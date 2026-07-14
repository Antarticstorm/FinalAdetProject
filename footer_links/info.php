<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");
?>

<style>
    /* ==========================================================================
       DESIGN ALIGNMENT FRAMEWORK (INDEX.PHP CONSISTENCY)
       ========================================================================== */
    
    /* 1. Reset & stabilize the header wrapper */
    main.page-content {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        height: auto !important;
        min-height: 100vh !important;
        padding: 40px 0 80px 0 !important;
        overflow: visible !important;
        background: transparent !important;
    }

    /* 2. Global Typography & Structural Polish */
    .info-section-wrapper {
        text-align: center;
        padding: 80px 0;
        width: 100%;
        display: block !important;
    }

    /* Match the tiny yellow/gold uppercase subsection markers from index.php */
    .info-section-wrapper .section-tag {
        font-family: 'Inter', sans-serif;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 2px;
        color: #d97706; /* Warm gold tint matching your landing page */
        margin-bottom: 12px;
        text-transform: uppercase;
    }

    /* Match the bold serif luxury titles */
    .info-section-wrapper .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.25rem;
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 32px;
    }

    /* 3. The Content Containers (Refining the Cards) */
    .info-text-block {
        max-width: 750px;
        margin: 0 auto;
        text-align: left;
        background: rgba(30, 41, 59, 0.7); /* Sleek translucent slate */
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.08); /* Sophisticated ultra-thin border */
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
    }

    /* Paragraph Text Styles */
    .info-text-block p {
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        line-height: 1.7;
        color: #cbd5e1; /* Smooth text readability */
        margin-bottom: 20px;
    }
    .info-text-block p:last-child {
        margin-bottom: 0;
    }

    .info-text-block strong {
        color: #f1f5f9;
        font-weight: 600;
    }

    /* Subheaders inside the cards (e.g., Privacy / Terms numbers) */
    .info-text-block h3 {
        font-family: 'Playfair Display', serif;
        margin-top: 32px;
        margin-bottom: 12px;
        font-size: 1.35rem;
        font-weight: 600;
        color: #f8fafc;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding-bottom: 6px;
    }
    .info-text-block h3:first-of-type {
        margin-top: 0;
    }

    /* Metadata / Timestamp tags */
    .info-meta-date {
        font-family: 'Inter', sans-serif;
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px;
        display: block;
    }

    /* Contact Details Grid Layout */
    .contact-details-grid {
        border-top: 1px dashed rgba(255, 255, 255, 0.1); 
        padding-top: 24px; 
        margin-top: 24px;
    }
    .contact-details-grid p {
        margin-bottom: 10px;
        font-size: 0.9rem;
    }
    .contact-details-grid strong {
        color: #d97706; /* Gold labels */
        display: inline-block;
        width: 180px;
    }

    /* Custom Page Smooth Scrolling */
    html {
        scroll-behavior: smooth;
    }
</style>

<!-- ===========================================
     ABOUT SECTION
=========================================== -->
<section id="about" class="why-section reveal info-section-wrapper">
    <div class="container">
        <p class="section-tag">Explore</p>
        <h2 class="section-title">About The Literary Nook</h2>
        
        <div class="info-text-block">
            <p>Welcome to <strong>The Literary Nook</strong>, your ultimate digital sanctuary for stories, literature, and knowledge. Curated with a deep passion for the written word, our platform serves as a modern harbor for book lovers and avid readers alike.</p>
            <p>Inspired by rich, immersive digital interfaces, we bridge the gap between classic physical tracking and seamless web cataloging to bring your collection straight to your screen.</p>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- ===========================================
     CONTACT SECTION
=========================================== -->
<section id="contact" class="why-section reveal info-section-wrapper">
    <div class="container">
        <p class="section-tag">Get In Touch</p>
        <h2 class="section-title">Contact Information</h2>
        
        <div class="info-text-block">
            <p>Have questions, technical feedback, or programmatic partnership opportunities? Our support network is monitoring incoming logs to assist you.</p>
            
            <div class="contact-details-grid">
                <p><strong>Email Support:</strong> support@literarynook.local</p>
                <p><strong>Laboratory Location:</strong> Manila, Metro Manila, Philippines</p>
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
        <p class="section-tag">Legal Compliance</p>
        <h2 class="section-title">Privacy Policy</h2>
        
        <div class="info-text-block">
            <span class="info-meta-date">Last Updated: July 2026</span>
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
        <p class="section-tag">Legal Agreement</p>
        <h2 class="section-title">Terms of Service</h2>
        
        <div class="info-text-block">
            <span class="info-meta-date">Last Updated: July 2026</span>
            <p>By accessing or utilizing our system inventory environments, you explicitly agree to align with the terms specified herein:</p>
            
            <h3>1. Account Usage Verification</h3>
            <p>Users are responsible for safeguarding authorization tokens, session assignments, and login cookies. Standard accounts attempting to access restricted administrative logic strings face strict account termination parameters.</p>
            
            <h3>2. Limitation of Liability</h3>
            <p>This web application is delivered "as-is" for tracking purposes under developmental educational project guidelines.</p>
        </div>
    </div>
</section>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>