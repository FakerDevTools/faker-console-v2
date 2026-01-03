<?php

// Form submission processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{

    $name = trim($_POST['name'] ?? '');

    if (!$name) 
    {

        message_set('API key name is required.', 'error');
        header_redirect('/create');

    } 
    else 
    {

        $token = bin2hex(random_bytes(16));

        $stmt = $mysqli->prepare('
            INSERT INTO `keys` (
                name,
                token,
                user_id,
                created_at, 
                updated_at
            ) VALUES (
                ?, ?, ?, NOW(), NOW()
            )
        ');
        $stmt->bind_param('sss', $name, $token, $_SESSION['user_id']);

        if ($stmt->execute()) 
        {

            message_set('API key has been created.', 'success');
            header_redirect('/keys');

        } 
        else 
        {

            message_set('API key creation failed. Please try again.', 'error');
            header_redirect('/keys');

        }

        $stmt->close();

    }

}

define('MENU', 'keys');
define('TITLE', 'Keys');

include __DIR__ . '/templates/html_header.php';
include __DIR__ . '/templates/dashboard_header.php';

?>

<h1>Create API Key</h1>

<a href="/keys">Keys</a> / 
Create API Key

<hr>

<div class="w3-display-container" style="max-width:600px;">

    <form method="post" action="" id="registerForm" novalidate>
        <label class="w3-text-black" for="name"><i class="fas fa-key"></i> API Key Name</label>
        <input class="w3-input w3-border w3-margin-bottom" type="text" id="name" name="name">
        <div id="nameError" class="w3-text-red w3-small w3-margin-bottom"></div>
    </form>

    <button class="w3-button w3-black w3-margin-top" onclick="validateForm()">
        <i class="fas fa-plus"></i> Create Key
    </button>

</div>

<script>

async function validateForm(e) {

    var form = document.getElementById('registerForm');
    var name = document.getElementById('name').value.trim();

    let valid = true;

    nameError.textContent = '';

    if (!name) {
        nameError.textContent = 'Key name is required.';
        valid = false;
    }

    if(valid) {
        form.submit();
    }

}

</script>



<?php 

include __DIR__ . '/templates/dashboard_footer.php';
include __DIR__ . '/templates/html_footer.php';

