<?php

define('TITLE', 'Forgot Password');

// Redirect if already logged in
login_check();

// Form submission processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{

    $email = trim($_POST['email'] ?? '');

    if ($email) 
    {

        $stmt = $mysqli->prepare('
            SELECT id, first, last, email
            FROM users 
            WHERE email = ?
        ');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) 
        {

            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store token and expiry in DB (assumes columns exist)
            $stmt->bind_result($data['id'], $data['first'], $data['last'], $data['email']);
            $stmt->fetch();

            $stmt2 = $mysqli->prepare('
                UPDATE users SET 
                forgot_token = ?
                WHERE id = ?
            ');
            $stmt2->bind_param('si', $token, $data['id']);
            $stmt2->execute();
            $stmt2->close();

            // Send email
            $data['link'] = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . "/reset/token/$token";
            
            include(__DIR__ . '/messages/password_reset.php');

            mail_send($data['email'], $data['first'].' '.$data['last'], 'Password Reset Request', $message);

            message_set('A password reset link has been sent to your email address.', 'success');
            header_redirect('/login');

        } 
        else 
        {

            message_set('No account found with that email address.', 'error');
            header_redirect('/forgot');

        }

        $stmt->close();

    } 
    else 
    {

        message_set('Please enter your email address.', 'error');
        header_redirect('/forgot');

    }
    
}

include __DIR__ . '/templates/html_header.php';

?>

<div class="w3-display-container" style="min-height:100vh; max-width:400px;">
    <a href="/login">
        <img src="https://cdn.faker.ca/images@1.0.0/faker-logo-coloured-horizontal.png" alt="Faker Logo" class="w3-margin-bottom" style="max-width:300px;">
    </a>
    <form method="post" action="" id="forgotForm" novalidate>
        <?php message_get(); ?>
        <label class="w3-text-black" for="email"><i class="fas fa-envelope"></i> Email</label>
        <input class="w3-input w3-border w3-margin-bottom" type="text" id="email" name="email">
        <div id="emailError" class="w3-text-red w3-small w3-margin-bottom"></div>
    </form>

    <button class="w3-button w3-black w3-margin-top" onclick="validateForm()">
        <i class="fas fa-unlock-alt"></i> Send Reset Link
    </button>
    
    <div class="w3-margin-top">
        <a href="/login"><i class="fas fa-sign-in-alt"></i> Back to Login</a>
    </div>
</div>
<script>

async function validateForm(e) {

    var form = document.getElementById('forgotForm');
    var email = document.getElementById('email').value.trim();

    let valid = true;

    emailError.textContent = '';

    if (!email) {
        emailError.textContent = 'Email is required.';
        valid = false;
    } else if (!isValidEmail(email)) {
        emailError.textContent = 'Please enter a valid email address.';
        valid = false;
    } else if(await checkEmailExists(email) === false) {
        emailError.textContent = 'Email address not registered.';
        valid = false;
    }

    if(valid) {
        form.submit();
    }
    
}

</script>

<?php

include __DIR__ . '/templates/html_footer.php';
