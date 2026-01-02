<?php

unset($_SESSION['user_id']);

message_set('You have been logged out successfully.', 'success');

header_redirect('/login');