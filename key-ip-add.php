<?php

if(!isset($_GET['id']))
{
    message_set('No key specified.', 'error');
    header_redirect('/keys');
}

$stmt = $mysqli->prepare('
    SELECT id, name
    FROM `keys` 
    WHERE user_id = ? 
    AND id = ?
    LIMIT 1
'); 
$stmt->bind_param('ii', $_SESSION['user_id'], $_GET['id']);
$stmt->execute();
$stmt->store_result();

if(!$stmt->num_rows)
{
    message_set('Key not found.', 'error');
    header_redirect('/keys');
}

$stmt->bind_result($key_id, $name);
$stmt->fetch();

// Form submission processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{

    $address = trim($_POST['address'] ?? '');

    if (!$address) 
    {

        message_set('IP address is required.', 'error');
        header_redirect('/key-ip-add/id/' . $key_id);

    } 
    else 
    {

        $stmt = $mysqli->prepare('
            SELECT id, deleted_at
            FROM ips 
            WHERE address = ? 
            AND key_id = ?
        ');
        $stmt->bind_param('si', $address, $_GET['id']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($ip_id, $deleted_at);

        if($stmt->num_rows > 0) 
        {

            $stmt->fetch();
            $stmt->close();

            if($deleted_at)
            {

                $stmt = $mysqli->prepare('
                    UPDATE ips
                    SET deleted_at = NULL,
                    updated_at = NOW()
                    WHERE id = ?
                    AND key_id = ?
                    LIMIT 1
                ');
                $stmt->bind_param('ii', $ip_id, $_GET['id']);
                $stmt->execute();
                $stmt->close();

                message_set('This IP address was previously deleted, it has been restored.', 'error');
                header_redirect('/key/id/' . $key_id . '/tab/ip-restrictions');

            }

            message_set('This IP addres has already been restricted for this key.', 'error');
            header_redirect('/key/id/' . $key_id . '/tab/ip-restrictions');

        }

        $stmt->close();

        $stmt = $mysqli->prepare('
            INSERT INTO ips (
                address,
                type,
                key_id,
                created_at, 
                updated_at
            ) VALUES (
                ?, ?, ?, NOW(), NOW()
            )
        ');
        $stmt->bind_param('ssi', $_POST['address'], $_POST['type'], $_GET['id']);

        if ($stmt->execute()) 
        {

            message_set('IP restriction has been created.', 'success');
            header_redirect('/key/id/' . $key_id . '/tab/ip-restrictions');

        } 
        else 
        {

            message_set('IP restriction creation failed. Please try again.', 'error');
            header_redirect('/key/id/' . $key_id . '/tab/ip-restrictions');

        }

        $stmt->close();

    }

}

define('MENU', 'keys');
define('TAB', isset($_GET['tab']) ? $_GET['tab'] : 'details');
define('TITLE', $name);

include __DIR__ . '/templates/html_header.php';
include __DIR__ . '/templates/dashboard_header.php';

?>

<h1>Create IP Restriction</h1>

<a href="/keys">Keys</a> / 
<a href="/key">Keys</a> / 
Create API Key

<hr>

<div class="w3-display-container" style="max-width:600px;">

    <form method="post" action="" id="restrictionForm" novalidate>
        <label class="w3-text-black" for="address"><i class="fas fa-globe"></i> IP Address</label>
        <input class="w3-input w3-border w3-margin-bottom" type="text" id="address" name="address">
        <div id="addressError" class="w3-text-red w3-small w3-margin-bottom"></div>

        <label class="w3-text-black" for="type"><i class="fas fa-toggle-on"></i> Type</label>
        <select class="w3-input w3-border w3-margin-bottom" id="type" name="type">
            <option value="allow">Allow</option>
            <option value="block">Block</option>
        </select>
    </form>

    <button class="w3-button w3-black w3-margin-top" onclick="validateForm()">
        <i class="fas fa-plus"></i> Create IP Restriction
    </button>

</div>

<script>

async function validateForm(e) {

    var form = document.getElementById('restrictionForm');
    var address = document.getElementById('address').value.trim();
    var type = document.getElementById('type').value.trim();

    let valid = true;

    addressError.textContent = '';

    if (!address) {
        addressError.textContent = 'IP address is required.';
        valid = false;
    } else if (!isValidIP(address)) {
        addressError.textContent = 'Please enter a valid IP address.';
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

