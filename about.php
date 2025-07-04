<?php
require 'inc/_global/config.php';
require 'inc/_global/views/head_start.php';

$pageTitle = 'About Us';
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTSSL'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$baseUrl = rtrim($baseUrl, '/') . '/';

require 'includes/header.php';
?>

<!-- Page Content -->
<div class="content">
    <!-- Hero -->
    <div class="bg-image" style="background-image: url('assets/media/photos/photo23@2x.jpg');">
        <div class="bg-primary-dark-op">
            <div class="content content-full text-center py-7 pb-5">
                <h1 class="h2 text-white mb-2">
                    About <?php echo SITE_NAME; ?>
                </h1>
                <h2 class="h4 fw-normal text-white-75">
                    Your Trusted Source for Digital Products
                </h2>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Story -->
    <div class="content">
        <div class="row justify-content-center py-4">
            <div class="col-lg-10">
                <div class="block block-rounded">
                    <div class="block-content">
                        <div class="row justify-content-center py-4">
                            <div class="col-lg-10">
                                <h2 class="h3 mb-4">Our Story</h2>
                                <p>Founded in 2023, <?php echo SITE_NAME; ?> has quickly become a leading platform for digital products, serving thousands of satisfied customers worldwide. What started as a small project has grown into a comprehensive marketplace offering a wide range of digital solutions.</p>
                                <p>Our mission is to provide high-quality digital products that help individuals and businesses achieve their goals. We carefully curate our collection to ensure that every product meets our high standards of quality and value.</p>
                            </div>
                        </div>

                        <!-- Team -->
                        <div class="row justify-content-center py-4">
                            <div class="col-lg-10">
                                <h2 class="h3 mb-4">Our Team</h2>
                                <div class="row">
                                    <div class="col-md-4 mb-4">
                                        <div class="block block-rounded text-center">
                                            <div class="block-content bg-body-light">
                                                <img class="img-avatar img-avatar-thumb" src="assets/media/avatars/avatar10.jpg" alt="">
                                            </div>
                                            <div class="block-content block-content-full">
                                                <h3 class="h5 mb-0">John Smith</h3>
                                                <p class="text-muted">CEO & Founder</p>
                                                <p class="mb-0">With over 10 years of experience in the digital industry, John leads our team with vision and passion.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="block block-rounded text-center">
                                            <div class="block-content bg-body-light">
                                                <img class="img-avatar img-avatar-thumb" src="assets/media/avatars/avatar16.jpg" alt="">
                                            </div>
                                            <div class="block-content block-content-full">
                                                <h3 class="h5 mb-0">Sarah Johnson</h3>
                                                <p class="text-muted">Head of Product</p>
                                                <p class="mb-0">Sarah ensures that every product in our marketplace meets our high standards of quality.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="block block-rounded text-center">
                                            <div class="block-content bg-body-light">
                                                <img class="img-avatar img-avatar-thumb" src="assets/media/avatars/avatar7.jpg" alt="">
                                            </div>
                                            <div class="block-content block-content-full">
                                                <h3 class="h5 mb-0">Mike Chen</h3>
                                                <p class="text-muted">Customer Support</p>
                                                <p class="mb-0">Mike leads our support team, ensuring that every customer receives exceptional service.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END Team -->

                        <!-- Values -->
                        <div class="row justify-content-center py-4">
                            <div class="col-lg-10">
                                <h2 class="h3 mb-4">Our Values</h2>
                                <div class="row">
                                    <div class="col-md-4 mb-4">
                                        <div class="block block-rounded h-100">
                                            <div class="block-content text-center">
                                                <div class="item item-2x item-circle bg-primary-lighter text-primary mx-auto mb-3">
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <h3 class="h5">Quality</h3>
                                                <p>We are committed to offering only the highest quality digital products that deliver real value to our customers.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="block block-rounded h-100">
                                            <div class="block-content text-center">
                                                <div class="item item-2x item-circle bg-primary-lighter text-primary mx-auto mb-3">
                                                    <i class="fa fa-lock"></i>
                                                </div>
                                                <h3 class="h5">Security</h3>
                                                <p>Your security is our top priority. We use industry-standard encryption to protect your data and transactions.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="block block-rounded h-100">
                                            <div class="block-content text-center">
                                                <div class="item item-2x item-circle bg-primary-lighter text-primary mx-auto mb-3">
                                                    <i class="fa fa-headset"></i>
                                                </div>
                                                <h3 class="h5">Support</h3>
                                                <p>Our dedicated support team is always ready to assist you with any questions or issues you may have.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END Values -->

                        <!-- Testimonials -->
                        <div class="row justify-content-center py-4">
                            <div class="col-lg-10">
                                <h2 class="h3 mb-4">What Our Customers Say</h2>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="block block-rounded h-100">
                                            <div class="block-content">
                                                <p class="mb-3">"I've been using <?php echo SITE_NAME; ?> for all my digital product needs. The quality is consistently excellent, and the customer service is outstanding."</p>
                                                <div class="d-flex align-items-center">
                                                    <img class="img-avatar img-avatar32" src="assets/media/avatars/avatar1.jpg" alt="">
                                                    <div class="ms-3">
                                                        <div class="fw-semibold">Alex Johnson</div>
                                                        <div class="text-muted">Professional Designer</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="block block-rounded h-100">
                                            <div class="block-content">
                                                <p class="mb-3">"The variety of products available is impressive. I always find exactly what I need for my projects. Highly recommended!"</p>
                                                <div class="d-flex align-items-center">
                                                    <img class="img-avatar img-avatar32" src="assets/media/avatars/avatar13.jpg" alt="">
                                                    <div class="ms-3">
                                                        <div class="fw-semibold">Maria Garcia</div>
                                                        <div class="text-muted">Web Developer</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END Testimonials -->

                        <!-- CTA -->
                        <div class="row justify-content-center py-4">
                            <div class="col-lg-10 text-center">
                                <div class="block block-rounded bg-primary-lighter">
                                    <div class="block-content block-content-full">
                                        <h3 class="h4 mb-4">Join Our Growing Community</h3>
                                        <p class="mb-4">Become part of our community of digital creators and enthusiasts today.</p>
                                        <a href="signup.php" class="btn btn-primary mb-2">
                                            <i class="fa fa-user-plus opacity-50 me-1"></i> Create Account
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END CTA -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Story -->
</div>
<!-- END Page Content -->

<?php 
require 'includes/footer.php';
require 'inc/_global/views/page_end.php';
?>
