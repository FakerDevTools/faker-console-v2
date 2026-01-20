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

define('MENU', 'keys');
define('TAB', isset($_GET['tab']) ? $_GET['tab'] : 'details');
define('TITLE', $name);

include __DIR__ . '/templates/html_header.php';
include __DIR__ . '/templates/dashboard_header.php';

?>

<h1><?= htmlspecialchars($name) ?></h1>

<a href="/keys">Keys</a> / 
<a href="/key/id/<?= $key_id ?>"><?= htmlspecialchars($name) ?></a> /
<?= tab_text(isset($_GET['tab']) ? $_GET['tab'] : 'details') ?>

<hr>

<?php if(!isset($_GET['tab'])): ?>
    <span class="w3-text-black"><i class="fa fa-magnifying-glass"></i> Details</span> | 
<?php else: ?>
    <a href="/key/id/<?= $key_id ?>"><i class="fa fa-magnifying-glass"></i> Details</a> | 
<?php endif; ?>

<?php if(TAB == 'api-restrictions'): ?>
    <span class="w3-text-black"><i class="fa fa-cogs"></i> API Restrictions</span> | 
<?php else: ?>
    <a href="/key/id/<?= $key_id ?>/tab/api-restrictions"><i class="fa fa-cogs"></i> API Restrictions</a> | 
<?php endif; ?>

<?php if(TAB == 'ip-restrictions'): ?>
    <span class="w3-text-black"><i class="fa fa-globe"></i> IP Restrictions</span> | 
<?php else: ?>
    <a href="/key/id/<?= $key_id ?>/tab/ip-restrictions"><i class="fa fa-globe"></i> IP Restrictions</a> | 
<?php endif; ?>

<?php if(TAB == 'activity-log'): ?>
    <span class="w3-text-black"><i class="fa fa-list"></i> Activity Log</span>
<?php else: ?>
    <a href="/key/id/<?= $key_id ?>/tab/activity-log"><i class="fa fa-list"></i> Activity Logs</a>
<?php endif; ?>

<hr>



<?php if(!isset($_GET['tab'])): ?>

<h2>Key Details</h2>

<p>Key details content goes here.</p>



<?php elseif($_GET['tab'] == 'api-restrictions'): ?>

<h2>API Restrictions</h2>

<p>Restrict this API key based on API call:</p>

<?php

$stmt = $mysqli->prepare('
    SELECT id, name, (
        SELECT COUNT(*)
        FROM api_user
        WHERE api_user.api_id = apis.id
        AND api_user.user_id = ?
    ) AS user_enabled, (
        SELECT COUNT(*)
        FROM api_key
        WHERE api_key.api_id = apis.id
    ) AS key_enabled
    FROM apis
    HAVING user_enabled = 1
    ORDER BY name
');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($api_id, $name, $user_enabled, $key_enabled);
$stmt->store_result();

?>

<div class="w3-flex">

    <?php while($stmt->fetch()): ?>

        <div class="w3-flex-item w3-leftbar w3-margin-right w3-border-<?= ($key_enabled == 1 ? "green" : "red") ?>" style="flex: 1 1 25%; max-width: 25%;">
            <a href="#" onclick="toggleApi(<?= $api_id ?>, event)" class="w3-border" style="display:flex; align-items:center; padding:8px; border: height:100%;">
                <div style="flex:1; display:flex; flex-direction:column; justify-content:center; margin-left:10px;">
                    <h4 style="margin:0">
                        <input type="checkbox" name="api" value="<?= $api_id ?>" <?= ($key_enabled === 1 ? 'checked' : '') ?>>
                        <?= htmlspecialchars($name) ?>
                    </h4>
                </div>
            </a>
        </div>

    <?php endwhile; ?>

</div>

<script>

function toggleApi(api_id, e) {

    e.preventDefault();

    let selectedPlan = event.target;
    selectedPlan = selectedPlan.closest(".w3-flex-item");

    let checkbox = selectedPlan.querySelector('input[type="checkbox"]');
    let enabled = checkbox.checked ? 0 : 1;

    if(enabled) {

        checkbox.checked = true;
        selectedPlan.classList.remove('w3-border-red');
        selectedPlan.classList.add('w3-border-green');  

    } else {

        checkbox.checked = false;
        selectedPlan.classList.add('w3-border-red');
        selectedPlan.classList.remove('w3-border-green');  

    }

    fetch('/ajax/api-key-toggle', 
    {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'key=' + encodeURIComponent(<?= json_encode($_GET['id']) ?>) + 
            '&api=' + encodeURIComponent(api_id)
    })
    .then(response => response.json())
    .then(data => 
    {
        
    })
    .catch(error => 
    {
        
    });

}

</script>



<?php elseif($_GET['tab'] == 'ip-restrictions'): ?>

<h2>IP Restrictions</h2>

<?php

$stmt = $mysqli->prepare('
    SELECT id, address, type, created_at
    FROM `ips`
    WHERE key_id = ?
    ORDER BY type, created_at DESC
');
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$stmt->bind_result($ip_id, $address, $type, $created_at);
$stmt->store_result();

?>

<table class="w3-table w3-bordered w3-striped w3-margin-top">
    <thead>
        <tr class="w3-black">
            <th>IP Address</th>
            <th>Type</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        <?php if($stmt->num_rows == 0): ?>
            <tr>
                <td colspan="3">No IP restrictions have been added yet.</td>
            </tr>
        <?php else: ?>
            <?php while($stmt->fetch()): ?>
                <tr>
                    <td><?= htmlspecialchars($address) ?></td>
                    <td>
                        <a href="#" onclick="toggleType(<?= $ip_id ?>, event)">
                            <?= ($type == 'allow' ? 'Allow' : 'Block') ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($created_at) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </tbody>
</table>

<button class="w3-button w3-black w3-margin-top" onclick="window.location.href='/key-ip-add/id/<?= $key_id ?>'">
    <i class="fas fa-plus"></i> Create an IP Restriction
</button>

<?php elseif($_GET['tab'] == 'activity-log'): ?>

<h2>Activity Logs</h2>

<p>Activity logs content goes here.</p>

<?php endif; ?>

<?php 

include __DIR__ . '/templates/dashboard_footer.php';
include __DIR__ . '/templates/html_footer.php';

