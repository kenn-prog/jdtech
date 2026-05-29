/**
 * JDTech — AJAX Utilities
 * PURPOSE: Reusable functions for sending requests to the
 *          PHP backend without reloading the page.
 *
 * AJAX EXPLAINED FOR BEGINNERS:
 * "AJAX" stands for Asynchronous JavaScript And XML.
 * Today we use JSON instead of XML, but the name stuck.
 *
 * Without AJAX: User submits form → whole page reloads
 * With AJAX:    User submits form → JS sends data in background
 *               → PHP processes it → JS updates only the part
 *               of the page that changed. Much faster!
 *
 * Modern AJAX uses the Fetch API (built into all browsers).
 * We use async/await to make the code easy to read.
 */

/**
 * Send a GET request and return parsed JSON.
 *
 * Example:
 *   const data = await apiGet('api/products.php?action=get_products');
 *   console.log(data.products);
 */
function buildUrl(url) {
  if (!url) return url;
  if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('//') || url.startsWith('/')) {
    return url;
  }
  return `${window.APP_URL || ''}/${url.replace(/^\/+/, '')}`;
}

async function apiGet(url) {
  try {
    const response = await fetch(buildUrl(url));
    if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
    return await response.json();
  } catch (error) {
    console.error('apiGet error:', error);
    return { ok: false, msg: 'Request failed. Check your connection.' };
  }
}

/**
 * Send a POST request with form data and return parsed JSON.
 *
 * Example:
 *   const data = await apiPost('backend/login-process.php', {
 *     email: 'user@test.com',
 *     password: 'secret'
 *   });
 *   if (data.ok) redirectTo('dashboard.php');
 */
async function apiPost(url, data = {}) {
  try {
    const form = new FormData();
    Object.entries(data).forEach(([key, value]) => {
      if (value !== null && value !== undefined) {
        form.append(key, value);
      }
    });

    const response = await fetch(buildUrl(url), { method: 'POST', body: form });
    if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
    return await response.json();
  } catch (error) {
    console.error('apiPost error:', error);
    return { ok: false, msg: 'Request failed. Check your connection.' };
  }
}

/**
 * Logout the current user by calling the backend, then redirect.
 */
async function logoutUser() {
  const data = await apiPost('backend/logout.php');
  if (data.ok) {
    window.location.href = 'index.php';
  } else {
    alert(data.msg || 'Could not log out. Please try again.');
  }
}

/**
 * Check current login status on page load.
 * Useful for pages that need to update UI based on session.
 */
async function checkSession() {
  return await apiGet('api/auth.php?action=check');
}
