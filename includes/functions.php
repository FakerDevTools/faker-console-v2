<?php

function tab_text($text) {
 
    return ucwords(str_replace(['-', '_'], ' ', $text));

}

function pre($data)
{
    echo '<pre>' . htmlspecialchars(print_r($data, true)) . '</pre>';
}

function message_set($message, $type = 'success') 
{
	$_SESSION['message'] = $message;
	$_SESSION['type'] = $type;
}

function message_get() 
{
	if (!empty($_SESSION['message'])) 
{
		$type = $_SESSION['type'] ?? 'success';
		$class = $type === 'success' ? 'w3-green' : ($type === 'error' ? 'w3-red' : 'w3-yellow');
        $icon = $type === 'success' ? '<i class="fas fa-check-circle"></i> ' : ($type === 'error' ? '<i class="fas fa-times-circle"></i> ' : '<i class="fas fa-exclamation-circle"></i> ');
        echo '<div class="w3-panel ' . $class . ' w3-padding w3-margin-bottom">' . $icon . htmlspecialchars($_SESSION['message']) . '</div>';
		unset($_SESSION['message'], $_SESSION['type']);
	}
}

function header_redirect($url) 
{
	header('Location: ' . $url);
	exit;
}

// Send a plain text email using Brevo (Sendinblue) API
function mail_send($to_email, $to_name, $subject, $message) 
{

    $to_name = trim($to_name);

    // Configure API key authorization: api-key
    $config = Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', BREVO_API_KEY);

    $apiInstance = new Brevo\Client\Api\TransactionalEmailsApi(
        new GuzzleHttp\Client(),
        $config
    );
    $sendSmtpEmail = new Brevo\Client\Model\SendSmtpEmail();

    // Sender details
    $sendSmtpEmail['sender'] = [
        'name' => 'Faker Dev Tools',
        'email' => 'support@faker.ca'
    ];

    // Recipient details
    $sendSmtpEmail['to'] = [
        [
            'email' => $to_email,
            'name' => $to_name ? $to_name : $to_email
        ]
    ];

    // Email content and subject
    $sendSmtpEmail['subject'] = $subject;
    $sendSmtpEmail['htmlContent'] = $message;

    try 
    {
        $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
        // print_r($result);
        // echo "Email sent successfully!\n";
    } 
    catch (Exception $e) 
    {
        pre($sendSmtpEmail);
        echo 'Exception when calling TransactionalEmailsApi->sendTransacEmail: ', $e->getMessage(), PHP_EOL;
    }

}

// Redirects to dashboard if user is already logged in
function login_check() 
{
	if (!empty($_SESSION['user_id'])) 
{
		message_set('You are already logged in.', 'error');
		header_redirect('/dashboard');
	}
}