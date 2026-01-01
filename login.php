<?php

define('TITLE', 'Login');

include __DIR__ . '/include/bootstrap.php';

include __DIR__ . '/templates/html_header.php';

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($email && $password) {
        $stmt = $mysqli->prepare('SELECT id, password FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $hash);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $_SESSION['user_id'] = $user_id;
                header('Location: /dashboard');
                exit;
            } else {
                $login_error = 'Invalid email or password.';
            }
        } else {
            $login_error = 'Invalid email or password.';
        }
        $stmt->close();
    } else {
        $login_error = 'Please enter both email and password.';
    }
}

?>

<div class="w3-display-container" style="min-height:100vh; max-width:400px;">
    
    <a href="/login">
        <img src="https://cdn.faker.ca/images@1.0.0/faker-logo-coloured-horizontal.png" alt="Faker Logo" class="w3-margin-bottom" style="max-width:300px;">
    </a>

    <form method="post" action="" id="loginForm" novalidate>

        <?php if (!empty($login_error)): ?>
            <div class="w3-panel w3-red w3-padding w3-round-large w3-margin-bottom"><?= $login_error ?></div>
        <?php endif; ?>
        
        <label class="w3-text-black" for="email"><i class="fas fa-envelope"></i> Email</label>
        <input class="w3-input w3-border w3-margin-bottom" type="text" id="email" name="email">
        <div id="emailError" class="w3-text-red w3-small w3-margin-bottom"></div>
        
        <label class="w3-text-black" for="password"><i class="fas fa-lock"></i> Password</label>
        <input class="w3-input w3-border w3-margin-bottom" type="password" id="password" name="password">
        <div id="passwordError" class="w3-text-red w3-small w3-margin-bottom"></div>
        
        <button class="w3-button w3-black w3-margin-top" type="submit">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>

    </form>

    <div class="w3-margin-top">
        <a href="/register"><i class="fas fa-user-plus"></i> Register</a> |
        <a href="/forgot"><i class="fas fa-unlock-alt"></i> Forgot Password?</a>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('loginForm');
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
            }
    });
});
</script>

<?php

include __DIR__ . '/templates/html_footer.php';
