// Redirects to dashboard if user is already logged in
function login_check() {
	if (!empty($_SESSION['user_id'])) {
		message_set('You are already logged in.', 'error');
		header_redirect('/dashboard');
	}
}
<?php

function message_set($message, $type = 'success') {
	$_SESSION['message'] = $message;
	$_SESSION['type'] = $type;
}

function message_get() {
	if (!empty($_SESSION['message'])) {
		$type = $_SESSION['type'] ?? 'success';
		$class = $type === 'success' ? 'w3-green' : ($type === 'error' ? 'w3-red' : 'w3-yellow');
        $icon = $type === 'success' ? '<i class="fas fa-check-circle"></i> ' : ($type === 'error' ? '<i class="fas fa-times-circle"></i> ' : '<i class="fas fa-exclamation-circle"></i> ');
        echo '<div class="w3-panel ' . $class . ' w3-padding w3-margin-bottom">' . $icon . htmlspecialchars($_SESSION['message']) . '</div>';
		unset($_SESSION['message'], $_SESSION['type']);
	}
}

function header_redirect($url) {
	header('Location: ' . $url);
	exit;
}