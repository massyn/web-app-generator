<?php

$config = [
    // ======= Application settings =======
    'title' => '${APP["title"]}',
    'index' => '<h1>Hello</h1><p>You have been logged on.</p>',
    'initial_username' => 'root@localhost', // Initial admin user account
    'initial_password' => 'secret123',      // Initial admin user password

    // ======= Database settings =======
    'prefix'    => 'prefix_',       // The table prefix to use, this trick allows you to have multiple tenants run on the same database.
    'create_schema' => True,       // If True, the create schema function will run all the time.  
                                    // Only run this the first time the app kicks off, then you can 
                                    // switch it off.

    // SQLite
    'database'  => 'sqlite',
    'file'      => 'c:/xampp/htdocs/test/mydb.sqlite',
    
    // mySQL
    // 'database'  => 'mysql',
    // 'host'      => 'localhost',
    // 'port'      => 3306,
    // 'db'        => 'dev',
    // 'username'  => 'root',
    // 'password'  => '',

    // Flat files
    // Try to avoid these if at all possible.  They're only provided as a means to test things.
    // 'database'  => 'flatfile',
    // 'datapath'  => 'c:/xampp/htdocs/test/db',
]

?>