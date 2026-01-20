// Validate if a string is a valid IPv4 or IPv6 address
function isValidIP(ip) {
    // IPv4 regex
    const ipv4 = /^(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}$/;
    // IPv6 regex (simple, not exhaustive)
    const ipv6 = /^([\da-fA-F]{1,4}:){7}[\da-fA-F]{1,4}$/;
    return ipv4.test(ip) || ipv6.test(ip);
}

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

