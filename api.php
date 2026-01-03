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

define('MENU', 'apis');
define('TAB', isset($_GET['tab']) ? $_GET['tab'] : 'details');
define('TITLE', $name);

include __DIR__ . '/templates/html_header.php';
include __DIR__ . '/templates/dashboard_header.php';

?>

<h1><?= htmlspecialchars($name) ?> API</h1>

<a href="/apis">APIS</a> / 
<a href="/api/id/<?= htmlspecialchars($slug) ?>"><?= htmlspecialchars($name) ?></a> / 
<?= tab_text(isset($_GET['tab']) ? $_GET['tab'] : 'details') ?>

<hr>

<?php if(!isset($_GET['tab'])): ?>
    <span class="w3-text-black"><i class="fa fa-magnifying-glass"></i> Details</span> | 
<?php else: ?>
    <a href="/api/id/<?= htmlspecialchars($slug) ?>"><i class="fa fa-magnifying-glass"></i> Details</a> | 
<?php endif; ?>

<?php if(TAB == 'documentation'): ?>
    <span class="w3-text-black"><i class="fa fa-book"></i> Documentation</span> | 
<?php else: ?>
    <a href="/api/id/<?= htmlspecialchars($slug) ?>/tab/documentation"><i class="fa fa-book"></i> Documentation</a> | 
<?php endif; ?>

<?php if(TAB == 'code-examples'): ?>
    <span class="w3-text-black"><i class="fa fa-code"></i> Code Examples</span>
<?php else: ?>
    <a href="/api/id/<?= htmlspecialchars($slug) ?>/tab/code-examples"><i class="fa fa-code"></i> Code Examples</a>
<?php endif; ?>

<hr> 

<?php if(!isset($_GET['tab'])): ?>

<h2>API Details</h2>

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



<?php elseif($_GET['tab'] == 'documentation'): ?>

<h2>Documentation</h2>

<p>Documentation content goes here.</p>



<?php elseif($_GET['tab'] == 'code-examples'): ?>

<h2>Examples</h2>

<p>Code examples content goes here.</p>

<?php endif; ?>

<?php 

include __DIR__ . '/templates/dashboard_footer.php';
include __DIR__ . '/templates/html_footer.php';

