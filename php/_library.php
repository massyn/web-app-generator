<?php

function param($param) {
    if(isset($_POST[$param])) {
        return $_POST[$param];
    } else {
        if(isset($_GET[$param])) {
            return $_GET[$param];
        } else {
            return null;
        }
    }
}

function doCSRF() {
    // CSRF
	// -- read the variable
    $csrf = param('_csrf');
    $_SESSION['csrf_valid'] = False;

    // -- check if it is the same
	if(isset($_SESSION['_csrf'])) {
		if($csrf == $_SESSION['_csrf']) {
			$_SESSION['csrf_valid'] = True;
		} 
	} 
	// -- reset the token
    if (PHP_MAJOR_VERSION < 7) {
	    $_SESSION['_csrf'] = bin2hex(random_bytes(30));
    } else {
        // This is not cryptographically secure.  You should really be running on a newer version of PHP..
        $strength = 30;
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length = strlen($permitted_chars);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        $_SESSION['_csrf'] = $random_string;
    }
}

function clientIP() {
    $fields = [
        'HTTP_X_FORWARDED_FOR',
        'HTTP_CLIENT_IP',
        'REMOTE_ADDR'
    ];

    foreach ($fields as $f) {
        if(isset($_SERVER[$f])) {
            return $_SERVER[$f];
        }
    }
}

function GUID() {
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

function authenticate($bs,$db) {
    if(isset($_SESSION['emailaddress'])) {
        if(param('func') == 'changepassword') {
            $bs->change_password_form([]);
            $bs->render();
            exit(0);
        }
        if(param('func') == 'change_password') {
            if(param('password1') != param('password2')) {
                $bs->alert('danger','Passwords do not match');
                $bs->change_password_form([]);
                $bs->render();
                exit(0);
            }
            if(strlen(param('password1')) <= 8) {
                $bs->alert('danger','Password is too short.');
                $bs->change_password_form([]);
                $bs->render();
                exit(0);
            }

            if($db->modify('_users', [
                'id' => $_SESSION['uid'],
                'password' => password_hash(param('password1'),PASSWORD_BCRYPT, [ 'cost' => 12])
            ])) {
                $bs->alert('info','Password changed');
            } else {
                $bs->alert('danger','There was a database error updating the password');
            }
        }

        if(param('func') == 'logout') {
            $bs->alert('info','Logged out successfully');
            session_destroy();
            session_start();
        } else {
            return True;
        }
    }
    
    if(param('emailaddress') && param('password') && $_SESSION['csrf_valid']) {
        $user_record = $db->search_record('_users','emailaddress',param('emailaddress'));
        if($user_record == null) {
            $bs->alert('danger','Access denied');
        } else {
            if (password_verify(param('password'), $user_record['password'])) {
                $_SESSION['emailaddress'] = param('emailaddress');
                $_SESSION['uid'] = $user_record['id'];
                return True;
            } else {
                $bs->alert('danger','Access denied');
            }
        }
    }
    
    $bs->login_form([
        'title' => 'Log in',
        'submit' => 'Log in',
        //'remember' => 'Remember me?',
        //'create' => [ 'Create a new account','create.php' ],
        //'forget' => [ 'Forget password?', 'forget.php' ]
    ]);
    $bs->render();
    exit(0);
    
}

// Credit where credit is due
// https://www.php.net/manual/en/function.asort.php#71318
function record_sort($records, $field, $reverse=false)
{
    $hash = [];
    foreach($records as $record) {
        if(isset($record[$field])) {
            $hash[$record[$field]] = $record;
        }
    }
    ($reverse)? krsort($hash) : ksort($hash);
    $records = [];
    foreach($hash as $record) {
        $records []= $record;
    }
    return $records;
}

?>