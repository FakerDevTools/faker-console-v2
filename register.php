<?php

define('TITLE', 'Register');

// Redirect if already logged in
login_check();

// Form submission processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        message_set('Please enter a valid email address.', 'error');
        header_redirect('/register');
    } elseif (!$password) {
        message_set('Password is required.', 'error');
        header_redirect('/register');
    } else {

        $stmt = $mysqli->prepare('
            SELECT id 
            FROM users 
            WHERE email = ?
        ');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            message_set('Email already registered.', 'error');
            header_redirect('/register');
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare('
                INSERT INTO users (
                    email, 
                    password, 
                    created_at, 
                    updated_at
                ) VALUES (
                    ?, ?, NOW(), NOW()
                )
            ');
            $stmt->bind_param('ss', $email, $hash);

            if ($stmt->execute()) {
                message_set('Registration successful! You can now login.', 'success');
                header_redirect('/login');
            } else {
                message_set('Registration failed. Please try again.', 'error');
                header_redirect('/register');
            }
        }
        $stmt->close();
    }
}

include __DIR__ . '/templates/html_header.php';

?>

<div class="w3-display-container" style="min-height:100vh; max-width:400px;">

    <a href="/login">
        <img src="https://cdn.faker.ca/images@1.0.0/faker-logo-coloured-horizontal.png" alt="Faker Logo" class="w3-margin-bottom" style="max-width:300px;">
    </a>

    <?php message_get(); ?>

    <form method="post" action="" id="registerForm" novalidate>
        
        <label class="w3-text-black" for="email"><i class="fas fa-envelope"></i> Email</label>
        <input class="w3-input w3-border w3-margin-bottom" type="text" id="email" name="email">
        <div id="emailError" class="w3-text-red w3-small w3-margin-bottom"></div>
        
        <label class="w3-text-black" for="password"><i class="fas fa-lock"></i> Password</label>
        <input class="w3-input w3-border w3-margin-bottom" type="password" id="password" name="password">
        <div id="passwordError" class="w3-text-red w3-small w3-margin-bottom"></div>
        
    </form>

    <button class="w3-button w3-black w3-margin-top" onclick="validateForm()">
        <i class="fas fa-user-plus"></i> Register
    </button>

    <div class="w3-margin-top">
        <a href="/login" class=""><i class="fas fa-sign-in-alt"></i> Back to Login</a>
    </div>

</div>

<script>

async function validateForm(e) {

    var form = document.getElementById('registerForm');
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
    } else if(await checkEmailExists(email)) {
        emailError.textContent = 'Email address already registered.';
        valid = false;
    }

    if (!password) {
        passwordError.textContent = 'Password is required.';
        valid = false;
    }
    else if(passwordConfirm.length < 8) 
    {
        passwordConfirmError.textContent = 'Password must be at least 8 characters long.';
        valid = false;
    } else if (!isStrongPassword(password)) {
        passwordError.textContent = 'Password must have at least one lowercase letter, one uppercase letter, one number, and one special character.';
        valid = false;
    }

    if(valid) {
        form.submit();
    }

}

</script>

<?php 

include __DIR__ . '/templates/html_footer.php'; 
