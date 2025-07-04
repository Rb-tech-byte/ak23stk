<?php
require 'inc/_global/config.php';
require 'inc/_global/views/head_start.php';

$pageTitle = 'Privacy Policy';
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$baseUrl = rtrim($baseUrl, '/') . '/';

require 'includes/header.php';
?>

<!-- Page Content -->
<div class="content">
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title">Privacy Policy</h3>
        </div>
        <div class="block-content">
            <div class="row justify-content-center py-4">
                <div class="col-lg-10">
                    <h2 class="h4 mb-4">Last Updated: <?php echo date('F j, Y'); ?></h2>
                    
                    <div class="mb-5">
                        <h3 class="h5 mb-3">1. Introduction</h3>
                        <p>Welcome to <?php echo SITE_NAME; ?>. We respect your privacy and are committed to protecting your personal data. This privacy policy will inform you about how we look after your personal data when you visit our website and tell you about your privacy rights and how the law protects you.</p>
                    </div>
                    
                    <div class="mb-5">
                        <h3 class="h5 mb-3">2. Information We Collect</h3>
                        <p>We may collect, use, store and transfer different kinds of personal data about you which we have grouped together as follows:</p>
                        <ul>
                            <li><strong>Identity Data</strong> includes first name, last name, username or similar identifier.</li>
                            <li><strong>Contact Data</strong> includes email address and telephone numbers.</li>
                            <li><strong>Technical Data</strong> includes internet protocol (IP) address, browser type and version, time zone setting and location, browser plug-in types and versions, operating system and platform, and other technology on the devices you use to access this website.</li>
                            <li><strong>Usage Data</strong> includes information about how you use our website, products, and services.</li>
                            <li><strong>Marketing and Communications Data</strong> includes your preferences in receiving marketing from us and our third parties and your communication preferences.</li>
                        </ul>
                    </div>
                    
                    <div class="mb-5">
                        <h3 class="h5 mb-3">3. How We Use Your Data</h3>
                        <p>We will only use your personal data when the law allows us to. Most commonly, we will use your personal data in the following circumstances:</p>
                        <ul>
                            <li>To register you as a new customer.</li>
                            <li>To process and deliver your orders including managing payments, fees, and charges.</li>
                            <li>To manage our relationship with you which will include notifying you about changes to our terms or privacy policy.</li>
                            <li>To administer and protect our business and this website (including troubleshooting, data analysis, testing, system maintenance, support, reporting, and hosting of data).</li>
                            <li>To deliver relevant website content and advertisements to you and measure or understand the effectiveness of the advertising we serve to you.</li>
                            <li>To use data analytics to improve our website, products/services, marketing, customer relationships, and experiences.</li>
                        </ul>
                    </div>
                    
                    <div class="mb-5">
                        <h3 class="h5 mb-3">4. Data Security</h3>
                        <p>We have put in place appropriate security measures to prevent your personal data from being accidentally lost, used, or accessed in an unauthorized way, altered, or disclosed. In addition, we limit access to your personal data to those employees, agents, contractors, and other third parties who have a business need to know.</p>
                    </div>
                    
                    <div class="mb-5">
                        <h3 class="h5 mb-3">5. Data Retention</h3>
                        <p>We will only retain your personal data for as long as necessary to fulfill the purposes we collected it for, including for the purposes of satisfying any legal, accounting, or reporting requirements.</p>
                    </div>
                    
                    <div class="mb-5">
                        <h3 class="h5 mb-3">6. Your Legal Rights</h3>
                        <p>Under certain circumstances, you have rights under data protection laws in relation to your personal data, including the right to:</p>
                        <ul>
                            <li>Request access to your personal data.</li>
                            <li>Request correction of your personal data.</li>
                            <li>Request erasure of your personal data.</li>
                            <li>Object to processing of your personal data.</n                            <li>Request restriction of processing your personal data.</li>
                            <li>Request transfer of your personal data.</li>
                            <li>Right to withdraw consent.</li>
                        </ul>
                    </div>
                    
                    <div class="mb-5">
                        <h3 class="h5 mb-3">7. Third-Party Links</h3>
                        <p>This website may include links to third-party websites, plug-ins, and applications. Clicking on those links or enabling those connections may allow third parties to collect or share data about you. We do not control these third-party websites and are not responsible for their privacy statements.</p>
                    </div>
                    
                    <div class="mb-5">
                        <h3 class="h5 mb-3">8. Contact Us</h3>
                        <p>If you have any questions about this privacy policy or our privacy practices, please contact us at:</p>
                        <address>
                            <strong><?php echo SITE_NAME; ?></strong><br>
                            Email: <?php echo SITE_EMAIL; ?><br>
                            Phone: <?php echo SITE_PHONE; ?>
                        </address>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END Page Content -->

<?php 
require 'includes/footer.php';
require 'inc/_global/views/page_end.php';
?>
