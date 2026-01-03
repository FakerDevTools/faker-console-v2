<?php

define('MENU', 'apis');
define('TITLE', 'APIs');

include __DIR__ . '/templates/html_header.php';
include __DIR__ . '/templates/dashboard_header.php';

?>

<h1>API Library</h1>

<?php

// List APIs
$stmt = $mysqli->prepare('
    SELECT id, name, description, icon, slug, (
        SELECT COUNT(*)
        FROM api_user
        WHERE api_user.api_id = apis.id
        AND api_user.user_id = ?
    ) AS status
    FROM apis 
    WHERE status = 1
    ORDER BY name ASC
');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($api_id, $name, $description, $icon, $slug, $status);

while($stmt->fetch()) {
    
    ?>
    <div class="w3-leftbar w3-margin-bottom w3-border-<?= ($status ? "green" : "red") ?>">
        <a href="/api/id/<?= $slug ?>" class="w3-border" style="display:flex; align-items:center; padding:8px; border:">
        <div style="flex:0 0 100px; display:flex; align-items:center; justify-content:center;">
            <img src="<?= $icon ?>" style="max-width:80%;">
        </div>

        <div style="flex:1; display:flex; flex-direction:column; justify-content:center; margin-left:10px;">
            <h4 style="margin:0"><?= htmlspecialchars($name) ?></h4>
            <p style="margin:0" class="w3-text-grey"><?= htmlspecialchars($description) ?></p>
        </div>
        </a>
        
    </div>
    <?php
}

?>

<?php 

include __DIR__ . '/templates/dashboard_footer.php';
include __DIR__ . '/templates/html_footer.php';

