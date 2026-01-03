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
<?= htmlspecialchars($name) ?>

<hr>

<?php if(!isset($_GET['tab'])): ?>
    <span class="w3-text-black"><i class="fa fa-magnifying-glass"></i> Details</span> | 
<?php else: ?>
    <a href="/key/id/<?= htmlspecialchars($key_id) ?>"><i class="fa fa-magnifying-glass"></i> Details</a> | 
<?php endif; ?>

<?php if(TAB == 'docs'): ?>
    <span class="w3-text-black"><i class="fa fa-cogs"></i> API Restrictions</span> | 
<?php else: ?>
    <a href="/key/id/<?= htmlspecialchars($key_id) ?>/tab/docs"><i class="fa fa-cogs"></i> API Restrictions</a> | 
<?php endif; ?>

<?php if(TAB == 'examples'): ?>
    <span class="w3-text-black"><i class="fa fa-globe"></i> IP Restrictions</span> | 
<?php else: ?>
    <a href="/key/id/<?= htmlspecialchars($key_id) ?>/tab/examples"><i class="fa fa-globe"></i> IP Restrictions</a> | 
<?php endif; ?>

<?php if(TAB == 'logs'): ?>
    <span class="w3-text-black"><i class="fa fa-list"></i> Activity Log</span>
<?php else: ?>
    <a href="/key/id/<?= htmlspecialchars($key_id) ?>/tab/logs"><i class="fa fa-list"></i> Activity Logs</a>
<?php endif; ?>

<hr>



<?php if(!isset($_GET['tab'])): ?>

<h2>Key Details</h2>

<p>Key details content goes here.</p>



<?php elseif($_GET['tab'] == 'docs'): ?>

<h2>Documentation</h2>

<p>Documentation content goes here.</p>



<?php elseif($_GET['tab'] == 'examples'): ?>

<h2>Examples</h2>

<p>Code examples content goes here.</p>



<?php elseif($_GET['tab'] == 'logs'): ?>

<h2>Activity Logs</h2>

<p>Activity logs content goes here.</p>

<?php endif; ?>

<?php 

include __DIR__ . '/templates/dashboard_footer.php';
include __DIR__ . '/templates/html_footer.php';

