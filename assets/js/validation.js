/**
 * JDTech — Client-Side Form Validation
 * PURPOSE: Validate form inputs BEFORE sending to the server.
 *
 * IMPORTANT: Client-side validation is for UX (speed/convenience).
 * ALWAYS also validate on the server (PHP) because JS can be bypassed.
 * Never trust user input on the server, even if JS validated it.
 */

// ── Validation Helpers ─────────────────────────────────────

/**
 * Check if a value is empty (after trimming whitespace).
 */
function isEmpty(value) {
  return !value || value.trim() === '';
}

/**
 * Check if an email address looks valid.
 */
function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
}

/**
 * Check if a password meets minimum requirements.
 */
function isValidPassword(password) {
  return password && password.length >= 8;
}

/**
 * Show an error message below a form field.
 * Adds a red border to the input and shows error text.
 */
function showFieldError(inputEl, message) {
  inputEl.classList.add('input-error');
  // Remove any existing error
  const existing = inputEl.parentElement.querySelector('.field-error');
  if (existing) existing.remove();
  // Add new error message
  const errorEl = document.createElement('p');
  errorEl.className   = 'field-error';
  errorEl.textContent = message;
  inputEl.parentElement.appendChild(errorEl);
}

/**
 * Clear the error state from a form field.
 */
function clearFieldError(inputEl) {
  inputEl.classList.remove('input-error');
  const errorEl = inputEl.parentElement.querySelector('.field-error');
  if (errorEl) errorEl.remove();
}

/**
 * Validate all inputs in a form and return true if all pass.
 * Adds/removes error messages automatically.
 *
 * Usage:
 *   const ok = validateForm(document.getElementById('myForm'));
 *   if (ok) submitForm();
 */
function validateForm(formEl) {
  let isValid = true;

  formEl.querySelectorAll('[required]').forEach(input => {
    if (isEmpty(input.value)) {
      showFieldError(input, 'This field is required.');
      isValid = false;
    } else if (input.type === 'email' && !isValidEmail(input.value)) {
      showFieldError(input, 'Please enter a valid email address.');
      isValid = false;
    } else if (input.type === 'password' && input.dataset.minlength && input.value.length < input.dataset.minlength) {
      showFieldError(input, `Password must be at least ${input.dataset.minlength} characters.`);
      isValid = false;
    } else {
      clearFieldError(input);
    }
  });

  return isValid;
}

// ── Real-time field validation (as user types) ─────────────
document.addEventListener('input', function(e) {
  const input = e.target;
  if (!input.hasAttribute('required')) return;

  if (!isEmpty(input.value)) {
    clearFieldError(input);
  }
});
