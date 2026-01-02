<?php
define('TITLE', 'Dashboard');
include __DIR__ . '/templates/html_header.php';
?>
<div class="w3-display-container" style="min-height:100vh;">
    <div class="w3-display-middle w3-container w3-left-align w3-padding w3-card w3-round-large" style="max-width:400px;">
        <h2 class="w3-center">Welcome to your dashboard!</h2>
        <p class="w3-center">You are now logged in.</p>
        <div class="w3-center w3-margin-top">
            <a href="/logout" class="w3-button w3-red w3-small"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</div>
<?php include __DIR__ . '/templates/html_footer.php'; ?>
