// Check if email exists via AJAX. Returns a Promise that resolves to true/false.
function checkEmailExists(email) {
    return fetch('/ajax/email-exists.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'email=' + encodeURIComponent(email)
    })
    .then(response => response.json())
    .then(data => {
        if (typeof data.exists !== 'undefined') {
            return data.exists;
        }
        return false;
    })
    .catch(() => false);
}
// Email validation function
function isValidEmail(email) {
    // Simple regex for demonstration; adjust as needed for stricter validation
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
