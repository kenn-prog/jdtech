<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

$pageTitle = 'FAQ';
$metaDescription = 'Browse frequently asked questions about ordering, shipping, account access, and support at JDTech.';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main class="page">
  <section class="page-header">
    <div class="container">
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= APP_URL ?>/index.php">Home</a>
        <span> / </span>
        <span>FAQ</span>
      </nav>
      <h1>Frequently Asked Questions</h1>
      <p>Find answers to our most common questions about ordering, delivery, returns, account access, and support.</p>
    </div>
  </section>

  <section class="container">
    <div class="page-card">
      <h2>Popular Questions</h2>
      <div class="faq-accordion" id="faqAccordion">
        <div style="margin-bottom:24px;">
          <h3 style="font-size:18px;margin-bottom:12px;color:var(--fg);">Ordering</h3>
        </div>
        <div class="faq-item">
          <button type="button" class="faq-button" aria-expanded="false" aria-controls="faq1">
            How do I place an order on JDTech?
            <span class="faq-icon">+</span>
          </button>
          <div id="faq1" class="faq-panel" role="region" aria-labelledby="faq1">
            <p>Browse products, add items to your cart, and complete checkout using your contact and delivery information. You will receive order confirmation once payment is received.</p>
          </div>
        </div>

        <div class="faq-item">
          <button type="button" class="faq-button" aria-expanded="false" aria-controls="faq2">
            What payment methods are accepted?
            <span class="faq-icon">+</span>
          </button>
          <div id="faq2" class="faq-panel" role="region" aria-labelledby="faq2">
            <p>We accept cash payments, bank transfers, and secure online payment options as listed during checkout. Exact payment methods may vary based on your selected order and location.</p>
          </div>
        </div>

        <div style="margin:32px 0 16px;">
          <h3 style="font-size:18px;margin-bottom:12px;color:var(--fg);">Shipping & Returns</h3>
        </div>
        <div class="faq-item">
          <button type="button" class="faq-button" aria-expanded="false" aria-controls="faq3">
            How long does shipping take?
            <span class="faq-icon">+</span>
          </button>
          <div id="faq3" class="faq-panel" role="region" aria-labelledby="faq3">
            <p>Delivery times depend on stock availability and your location. Most in-Metro Manila orders are fulfilled within 1–3 business days.</p>
          </div>
        </div>

        <div class="faq-item">
          <button type="button" class="faq-button" aria-expanded="false" aria-controls="faq4">
            What is your return policy?
            <span class="faq-icon">+</span>
          </button>
          <div id="faq4" class="faq-panel" role="region" aria-labelledby="faq4">
            <p>If a product arrives damaged or defective, please message us within the first 48 hours. We will review the issue and offer repair, replacement, or refund depending on the case.</p>
          </div>
        </div>

        <div style="margin:32px 0 16px;">
          <h3 style="font-size:18px;margin-bottom:12px;color:var(--fg);">Account & Support</h3>
        </div>
        <div class="faq-item">
          <button type="button" class="faq-button" aria-expanded="false" aria-controls="faq5">
            How can I update my account details?
            <span class="faq-icon">+</span>
          </button>
          <div id="faq5" class="faq-panel" role="region" aria-labelledby="faq5">
            <p>Go to your account profile once logged in to update delivery address, contact number, or password. If you need assistance, message us through Facebook Messenger.</p>
          </div>
        </div>

        <div class="faq-item">
          <button type="button" class="faq-button" aria-expanded="false" aria-controls="faq6">
            How do I contact customer support?
            <span class="faq-icon">+</span>
          </button>
          <div id="faq6" class="faq-panel" role="region" aria-labelledby="faq6">
            <p>For fast support, message us on Facebook Messenger or visit our Facebook page. Check the Contact page for hours and additional reply details.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const items = document.querySelectorAll('.faq-item');
      items.forEach((item) => {
        const button = item.querySelector('.faq-button');
        const panel = item.querySelector('.faq-panel');

        button.addEventListener('click', () => {
          const isOpen = item.classList.toggle('open');
          button.setAttribute('aria-expanded', String(isOpen));
          if (isOpen) {
            panel.classList.add('open');
            panel.style.maxHeight = panel.scrollHeight + 'px';
          } else {
            panel.classList.remove('open');
            panel.style.maxHeight = null;
          }
        });
      });
    });
  </script>
</main>

<?php include 'includes/footer.php'; ?>