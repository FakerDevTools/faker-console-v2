<?php

define('MENU', 'keys');
define('TITLE', 'Keys');

include __DIR__ . '/templates/html_header.php';
include __DIR__ . '/templates/dashboard_header.php';

?>

<h1>API Keys</h1>

<?php

// List APIs
$stmt = $mysqli->prepare('
    SELECT id, name, token, (
        SELECT COUNT(*)
        FROM ips
        WHERE key_id = `keys`.id
    ) AS ips_count, (
        SELECT COUNT(*)
        FROM api_key
        WHERE key_id = `keys`.id
    ) AS apis_count, (
        SELECT COUNT(*)
        FROM logs
        WHERE key_id = `keys`.id
    ) AS logs_count, (
        SELECT MAX(created_at)
        FROM logs
        WHERE key_id = `keys`.id
    ) AS logs_max
    FROM `keys` 
    WHERE user_id = ?
    ORDER BY created_at DESC
');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($key_id, $name, $token, $ips_count, $apis_count, $logs_count, $logs_max);

if($stmt->num_rows === 0) 
{

    echo '<p>You have not created an API key.</p>';
    
}
else
{

    while($stmt->fetch()) {
        
        ?>
        <div class="w3-leftbar w3-margin-bottom w3-border-grey">
            <a href="/key/id/<?= $key_id ?>" class="w3-border" style="display:flex; align-items:center; padding:8px; border:">

            <div style="flex:1; display:flex; flex-direction:column; justify-content:center; margin-left:10px;">
                <h4 style="margin:0"><?= htmlspecialchars($name) ?></h4>
                <p style="margin:0; font-size:0.9em; color:grey;">
                    <?php if($ips_count == 0 and $apis_count == 0): ?>
                        API key has no restrictions | 
                    <?php else: ?>
                    <?php endif; ?>
                    Logs: <?= $logs_count ?> | 
                    Last Used: <?= ($logs_max ? htmlspecialchars($logs_max) : 'Never') ?>

                </p>
                
            </div>
            </a>
            
        </div>
        <?php
    }

}

?>

<p><a href="/create">Create an API Key</a></p>

<?php 

include __DIR__ . '/templates/dashboard_footer.php';
include __DIR__ . '/templates/html_footer.php';

