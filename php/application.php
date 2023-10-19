<?php
<%include file="shebang.mako"/>

// application.php is the main control app executed before every script.  It is responsible for things
// like connecting to the database, loading the necessary libraries, ensuring security is maintained, and so on.

// TODO - Remove the errors before we go to production
error_reporting(E_ALL);
error_reporting(-1);

session_start();

$currDir = dirname(__FILE__);
include "$currDir/_bootstrap.php";
include "$currDir/_library.php";
include "$currDir/_database.php";
include "$currDir/config.php";
include "$currDir/create_schema.php";

date_default_timezone_set('UTC');
doCSRF();

$bs = new bootstrap();
$db = new database($config);

if($config['create_schema']) {
    // Create the internal user database
    $z = $db->create_table('_users');
    $db->create_field('_users','emailaddress','text');
    $db->create_field('_users','password','text');
    
    if(!$z) {
        $db->insert('_users',[
            'emailaddress' => $config['initial_username'],
            'password' => password_hash($config['initial_password'],PASSWORD_BCRYPT, [ 'cost' => 12]),
        ]);
    }

    create_schema($db);
}

$bs->att('title',$config['title']);

$bs->menu('Home','index.php');

authenticate($bs,$db);

% for Y in X['schema']:
$bs->menu('${Y}','${X['schema'][Y]['tag']}.php?_csrf=' . $_SESSION['_csrf'],'${X['schema'][Y]['parent']}');
% endfor
if (isset($_SESSION['emailaddress'])) {
    $bs->menu('Change Password','?func=changepassword',$_SESSION['emailaddress']);
    $bs->menu('Logout','?func=logout',$_SESSION['emailaddress']);
}

?>