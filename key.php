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
<a href="/key/id/<?= htmlspecialchars($key_id) ?>"><?= htmlspecialchars($name) ?></a> /
<?= tab_text(isset($_GET['tab']) ? $_GET['tab'] : 'details') ?>

<hr>

<?php if(!isset($_GET['tab'])): ?>
    <span class="w3-text-black"><i class="fa fa-magnifying-glass"></i> Details</span> | 
<?php else: ?>
    <a href="/key/id/<?= htmlspecialchars($key_id) ?>"><i class="fa fa-magnifying-glass"></i> Details</a> | 
<?php endif; ?>

<?php if(TAB == 'api-restrictions'): ?>
    <span class="w3-text-black"><i class="fa fa-cogs"></i> API Restrictions</span> | 
<?php else: ?>
    <a href="/key/id/<?= htmlspecialchars($key_id) ?>/tab/api-restrictions"><i class="fa fa-cogs"></i> API Restrictions</a> | 
<?php endif; ?>

<?php if(TAB == 'ip-restrictions'): ?>
    <span class="w3-text-black"><i class="fa fa-globe"></i> IP Restrictions</span> | 
<?php else: ?>
    <a href="/key/id/<?= htmlspecialchars($key_id) ?>/tab/ip-restrictions"><i class="fa fa-globe"></i> IP Restrictions</a> | 
<?php endif; ?>

<?php if(TAB == 'activity-log'): ?>
    <span class="w3-text-black"><i class="fa fa-list"></i> Activity Log</span>
<?php else: ?>
    <a href="/key/id/<?= htmlspecialchars($key_id) ?>/tab/activity-log"><i class="fa fa-list"></i> Activity Logs</a>
<?php endif; ?>

<hr>



<?php if(!isset($_GET['tab'])): ?>

<h2>Key Details</h2>

<p>Key details content goes here.</p>



<?php elseif($_GET['tab'] == 'api-restrictions'): ?>

<h2>API Restrictions</h2>

<p>API restrictions content goes here.</p>


<?php elseif($_GET['tab'] == 'ip-restrictions'): ?>

<h2>IP Restrictions</h2>

<p>IP restrictions content goes here.</p>


<?php elseif($_GET['tab'] == 'activity-log'): ?>

<h2>Activity Logs</h2>

<p>Activity logs content goes here.</p>

<?php endif; ?>

<?php 

include __DIR__ . '/templates/dashboard_footer.php';
include __DIR__ . '/templates/html_footer.php';

