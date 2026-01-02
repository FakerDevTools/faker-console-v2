<?php

define('TITLE', 'Register');

// Redirect if already logged in
login_check();

// Page requirements validation
if(!isset($_GET['token']) || empty($_GET['token'])) 
{

    message_set('Invalid or missing password reset token.', 'error');
    header_redirect('/login');

}

$stmt = $mysqli->prepare('
    SELECT id 
    FROM users
    WHERE forgot_token = ?
    LIMIT 1
');

$stmt->bind_param('s', $_GET['token']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) 
{

    message_set('Invalid token provided.', 'error');
    header_redirect('/login');

}

// Form submission processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{

    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['passwordConfirm'] ?? '');

    if (!$password or !$password_confirm) 
    {

        message_set('Password is required.', 'error');
        header_redirect('/reset/token/' . htmlspecialchars($_GET['token']));

    }
    elseif($password !== $password_confirm) 
    {

        message_set('Passwords do not match.', 'error');
        header_redirect('/reset/token/' . htmlspecialchars($_GET['token']));

    } 
    else 
    {

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare('
            UPDATE users SET 
            password = ?,
            forgot_token = NULL,
            updated_at = NOW()
            WHERE forgot_token = ?
        ');
        $stmt->bind_param('ss', $hash, $_GET['token']);

        message_set('Password has been successfully updated! You can now login.', 'success');
        header_redirect('/login');
        
    }

}

include __DIR__ . '/templates/html_header.php';

?>

<div class="w3-display-container" style="min-height:100vh; max-width:400px;">

    <a href="/login">
        <img src="https://cdn.faker.ca/images@1.0.0/faker-logo-coloured-horizontal.png" alt="Faker Logo" class="w3-margin-bottom" style="max-width:300px;">
    </a>

    <?php message_get(); ?>

    <form method="post" action="" id="passwordForm" novalidate>
        
        <label class="w3-text-black" for="password"><i class="fas fa-lock"></i> New Password</label>
        <input class="w3-input w3-border w3-margin-bottom" type="password" id="password" name="password">
        <div id="passwordError" class="w3-text-red w3-small w3-margin-bottom"></div>
        
        <label class="w3-text-black" for="passwordConfirm"><i class="fas fa-lock"></i> Confirm Password</label>
        <input class="w3-input w3-border w3-margin-bottom" type="password" id="passwordConfirm" name="passwordConfirm">
        <div id="passwordConfirmError" class="w3-text-red w3-small w3-margin-bottom"></div>
        
    </form>

    <button class="w3-button w3-black w3-margin-top" onclick="validateForm()">
        <i class="fas fa-lock"></i> Update Password
    </button>

    <div class="w3-margin-top">
        <a href="/login" class=""><i class="fas fa-sign-in-alt"></i> Back to Login</a>
    </div>

</div>

<script>

async function validateForm(e) {

    var form = document.getElementById('passwordForm');
    var password = document.getElementById('password').value.trim();
    var passwordConfirm = document.getElementById('passwordConfirm').value.trim();

    let valid = true;

    passwordError.textContent = '';
    passwordConfirmError.textContent = '';

    if (!password) {
        passwordError.textContent = 'Password is required.';
        valid = false;
    }

    if (!passwordConfirm) {
        passwordConfirmError.textContent = 'Please confirm your password.';
        valid = false;
    }
    else if (password && passwordConfirm && password !== passwordConfirm) {
        passwordConfirmError.textContent = 'Passwords do not match.';
        valid = false;
    }
    else if(passwordConfirm.length < 8) 
    {
        passwordConfirmError.textContent = 'Password must be at least 8 characters long.';
        valid = false;
    }
    else if (!isStrongPassword(passwordConfirm)) 
    {
        passwordConfirmError.textContent = 'Password must have at least one lowercase letter, one uppercase letter, one number, and one special character.';
        valid = false;
    }

    if(valid) {
        form.submit();
    }

}

</script>

<?php 

include __DIR__ . '/templates/html_footer.php'; 
