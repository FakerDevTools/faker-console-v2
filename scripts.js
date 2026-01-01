// Check if email exists via AJAX. Returns a Promise that resolves to true/false.
async function checkEmailExists(email) {
    try {
        const response = await fetch('/ajax/email-exists.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'email=' + encodeURIComponent(email)
        });

        const data = await response.json();

        if (typeof data.exists !== 'undefined') {
            return data.exists;
        }
        return false;
    } catch (e) {
        return false;
    }
}

// Email validation function
function isValidEmail(email) {
    // Simple regex for demonstration; adjust as needed for stricter validation
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
