<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

$pageTitle = 'Terms of Service';
$metaDescription = 'Review JDTech terms of service, website usage policies, limitations, and responsibilities before shopping.';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main class="page">
  <section class="page-header">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= APP_URL ?>/index.php">Home</a>
        <span> / </span>
        <span>Terms of Service</span>
      </nav>
      <h1>Terms of Service</h1>
      <p>These terms describe the rules and responsibilities for using JDTech, purchasing products, and interacting with our service.</p>
    </div>
  </section>

  <section class="container content-grid">
    <div class="page-card">
      <h2>1. Acceptance of Terms</h2>
      <p>By using JDTech, you agree to these terms and any updates posted on this page. If you do not agree, please do not use the website.</p>

      <h3>2. Website Use</h3>
      <ul>
        <li>You may use the website for lawful, personal shopping and information only.</li>
        <li>Unauthorized resale, data scraping, or misuse of any content is prohibited.</li>
        <li>We may suspend access if you violate these terms or attempt to compromise site operations.</li>
      </ul>

      <h3>3. Orders and Pricing</h3>
      <ul>
        <li>Product prices are listed on the site and may change without notice.</li>
        <li>Orders are subject to availability and confirmation. We reserve the right to refuse or cancel orders.</li>
        <li>Descriptions, photos, and specifications are provided for guidance and may differ slightly from the actual product.</li>
      </ul>

      <h3>4. Returns and Refunds</h3>
      <p>Requests for returns or refunds must be made within the timeframes specified in our return policy, and items must be returned in acceptable condition. Damaged or defective goods should be reported immediately.</p>

      <h3>5. Liability and Disclaimers</h3>
      <p>JDTech is not responsible for indirect, incidental, or consequential losses resulting from use of the website or purchased products. Our liability is limited to the value of the original purchase.</p>

      <h3>6. Intellectual Property</h3>
      <p>All text, images, logos, and design elements on JDTech are protected and owned by JDTech or our partners. You may not reproduce or redistribute content without permission.</p>

      <h3>7. Privacy</h3>
      <p>We collect and use personal information as described in our Privacy Policy. Visiting this site indicates that you accept the privacy practices outlined there.</p>

      <h3>8. Changes to Terms</h3>
      <p>We may update these terms at any time. The current version is posted on this page, with the effective date reflected in the page content.</p>
    </div>

    <aside class="contact-card" aria-label="Terms contact details">
      <h3>Need help?</h3>
      <p>If you have questions about our terms, feel free to reach out for clarification.</p>
      <div>
        <strong>Contact:</strong>
        <br />
        <a href="https://m.me/hernandezcomputertech" target="_blank" rel="noopener noreferrer">Facebook Messenger</a>
      </div>
      <div>
        <strong>Facebook Page:</strong>
        <br />
        <a href="https://www.facebook.com/hernandezcomputertech/" target="_blank" rel="noopener noreferrer">hernandezcomputertech</a>
      </div>
      <div>
        <strong>Support hours:</strong>
        <br />
        Monday–Saturday
      </div>
      <small>Contact us if you need help with order terms, returns, or website policies.</small>
    </aside>
  </section>
</main>

<?php include 'includes/footer.php'; ?>