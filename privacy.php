<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

$pageTitle = 'Privacy Policy';
$metaDescription = 'Learn about JDTech privacy practices, how personal data is collected, cookies are used, and how we protect customer information.';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main class="page">
  <section class="page-header">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= APP_URL ?>/index.php">Home</a>
        <span> / </span>
        <span>Privacy Policy</span>
      </nav>
      <h1>Privacy Policy</h1>
      <p>JDTech respects your privacy and works to keep your personal information secure. This page explains how we collect, use, and protect your details while you shop with us.</p>
    </div>
  </section>

  <section class="container content-grid">
    <div class="page-card">
      <h2>Information We Collect</h2>
      <p>We gather information that helps us deliver a safer, more personalized experience. This may include:</p>
      <ul>
        <li>Contact details such as name, email, phone, and delivery address.</li>
        <li>Order history, purchase preferences, and product interests.</li>
        <li>Website activity including pages visited, search queries, and form interactions.</li>
      </ul>

      <h3>How We Use Your Data</h3>
      <ul>
        <li>Process and fulfill orders, manage returns, and respond to customer requests.</li>
        <li>Send order updates, promotions, and service notifications when permitted.</li>
        <li>Improve our website, product selection, and customer support services.</li>
      </ul>

      <h3>Cookies and Tracking</h3>
      <p>JDTech uses cookies and similar technologies to remember preferences, keep you signed in, and provide relevant content. Cookies help us analyze site traffic and improve the shopping experience.</p>
      <p>You can manage browser cookie settings at any time, but some parts of the site may not function properly if cookies are disabled.</p>

      <h3>Security and Protection</h3>
      <p>We use industry-standard practices to protect your information from unauthorized access and loss. Sensitive payment data is handled by trusted payment providers and never stored on our site in plain form.</p>

      <h3>Third-Party Services</h3>
      <p>Services such as payment gateways, analytics, and social messaging may collect data under their own privacy terms. We recommend reviewing those providers' policies in addition to ours.</p>

      <h3>Changes to This Policy</h3>
      <p>When we update this privacy policy, we will post the revised version on this page along with the effective date. Continued use of JDTech indicates acceptance of those changes.</p>
    </div>

    <aside class="contact-card" aria-label="Privacy contact details">
      <h3>Contact Information</h3>
      <p>If you have questions about privacy, data handling, or cookie settings, please get in touch.</p>
      <div>
        <strong>Message us:</strong>
        <br />
        <a href="https://m.me/hernandezcomputertech" target="_blank" rel="noopener noreferrer">Facebook Messenger</a>
      </div>
      <div>
        <strong>Facebook Page:</strong>
        <br />
        <a href="https://www.facebook.com/hernandezcomputertech/" target="_blank" rel="noopener noreferrer">hernandezcomputertech</a>
      </div>
      <div>
        <strong>Service hours:</strong>
        <br />
        Monday–Saturday
      </div>
      <small>We are committed to keeping your information safe and only using it to improve your shopping experience.</small>
    </aside>
  </section>
</main>

<?php include 'includes/footer.php'; ?>