<?php

define('TITLE', 'Register');

include __DIR__ . '/include/bootstrap.php';

include __DIR__ . '/templates/html_header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!$password) {
        $error = 'Password is required.';
    } else {
        // Check if email exists
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare('INSERT INTO users (email, password, created_at, updated_at) VALUES (?, ?, NOW(), NOW())');
            $stmt->bind_param('ss', $email, $hash);
            if ($stmt->execute()) {
                $success = 'Registration successful! You can now <a href="/login">login</a>.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
        $stmt->close();
    }
}

?>

<div class="w3-display-container" style="min-height:100vh; max-width:400px;">

    <a href="/login">
        <img src="https://cdn.faker.ca/images@1.0.0/faker-logo-coloured-horizontal.png" alt="Faker Logo" class="w3-margin-bottom" style="max-width:300px;">
    </a>

    <?php if ($success): ?>
        <div class="w3-panel w3-green w3-padding w3-round-large w3-margin-bottom"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="w3-panel w3-red w3-padding w3-round-large w3-margin-bottom"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" action="" id="registerForm" novalidate>
        
        <label class="w3-text-black" for="email"><i class="fas fa-envelope"></i> Email</label>
        <input class="w3-input w3-border w3-margin-bottom" type="text" id="email" name="email">
        <div id="emailError" class="w3-text-red w3-small w3-margin-bottom"></div>
        
        <label class="w3-text-black" for="password"><i class="fas fa-lock"></i> Password</label>
        <input class="w3-input w3-border w3-margin-bottom" type="password" id="password" name="password">
        <div id="passwordError" class="w3-text-red w3-small w3-margin-bottom"></div>
        
        <button class="w3-button w3-black w3-margin-top" type="submit">
            <i class="fas fa-user-plus"></i> Register
        </button>
    </form>

    <div class="w3-margin-top">
        <a href="/login" class=""><i class="fas fa-sign-in-alt"></i> Back to Login</a>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('registerForm');
    form.addEventListener('submit', function(e) {
        var email = document.getElementById('email').value.trim();
        var password = document.getElementById('password').value.trim();
        let valid = true;
        emailError.textContent = '';
        passwordError.textContent = '';
        if (!email) {
            emailError.textContent = 'Email is required.';
            valid = false;
        } else if (!isValidEmail(email)) {
            emailError.textContent = 'Please enter a valid email address.';
            valid = false;
        }
        if (!password) {
            passwordError.textContent = 'Password is required.';
            valid = false;
        }
        if (!valid) {
            e.preventDefault();
            return;
        }
        // AJAX check for email existence
        e.preventDefault();
        checkEmailExists(email).then(function(exists) {
            if (exists) {
                emailError.textContent = 'Email already registered.';
            } else {
                form.submit();
            }
        });
    });
});
</script>

<?php 

include __DIR__ . '/templates/html_footer.php'; 
