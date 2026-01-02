<?php

$message = <<<EOD

<p>Thank you for registering. Please verify your email by clicking the link below:</p>
<p><a href="{$data['link']}">{$data['link']}</a></p>
<p>If you did not request this, please ignore this email.</p>

EOD;