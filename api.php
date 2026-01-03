<?php

if(!isset($_GET['id']))
{
    message_set('No API specified.', 'error');
    header_redirect('/apis');
}

$stmt = $mysqli->prepare('
    SELECT id, name, description, icon, slug, (
        SELECT COUNT(*)
        FROM api_user 
        WHERE user_id = ? AND api_id = apis.id
    ) AS enabled
    FROM apis 
    WHERE slug = ?
    LIMIT 1
'); 
$stmt->bind_param('is', $_SESSION['user_id'], $_GET['id']);
$stmt->execute();
$stmt->store_result();

if(!$stmt->num_rows)
{
    message_set('API not found.', 'error');
    header_redirect('/apis');
}

$stmt->bind_result($api_id, $name, $description, $icon, $slug, $enabled);
$stmt->fetch();

define('TITLE', $name);

include __DIR__ . '/templates/html_header.php';
include __DIR__ . '/templates/dashboard_header.php';

?>

<h1><?= htmlspecialchars($name) ?> API</h1>

<a href="/apis">APIS</a> / 
<?= htmlspecialchars($name) ?>

<hr>

<input type="checkbox" class="w3-check" id="enable" <?= $enabled ? 'checked' : '' ?> />
<label for="enable" id="enableLabel"><?= $enabled ? 'This API is Enabled' : 'Enable this API' ?></label>

<p><?= htmlspecialchars($description) ?></p>

<script>

let enable = document.getElementById('enable');
let enableLabel = document.getElementById('enableLabel');

enable.addEventListener('change', function() 
{

    enable.disabled = true;

    if(enable.checked == true)
    {
        enableLabel.innerText = 'This API is Enabled';    
    }
    else
    {
        enableLabel.innerText = 'Enable this API';
    }

    fetch('/ajax/api-toggle', 
    {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'user=' + encodeURIComponent(<?= json_encode($_SESSION['user_id']) ?>) + 
            '&api=' + encodeURIComponent(<?= json_encode($api_id) ?>)
    })
    .then(response => response.json())
    .then(data => 
    {
        
    })
    .catch(error => 
    {
        
    });

    enable.disabled = false;

});

</script>



<?php 

include __DIR__ . '/templates/dashboard_footer.php';
include __DIR__ . '/templates/html_footer.php';

