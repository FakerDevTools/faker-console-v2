
// Check if email exists via AJAX. Returns a Promise that resolves to true/false.
async function checkEmailExists(email) {
    try {
        const response = await fetch('/ajax/email-exists.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'email=' + encodeURIComponent(email)
        });

        const data = await response.json();

        console.log(email);
        console.log(data);

        if (typeof data.exists === 'boolean') {
            if(data.exists === true) return true;
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

// Password must have at least one letter, one number, one capital, and one special character
function isStrongPassword(password) {
    const hasLetter = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasCapital = /[A-Z]/.test(password);
    const hasSpecial = /[^A-Za-z0-9]/.test(password);
    return hasLetter && hasNumber && hasCapital && hasSpecial;
}

