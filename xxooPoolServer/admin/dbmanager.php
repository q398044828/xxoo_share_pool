<?php

require_once __DIR__.'/../config.php';
require_once __DIR__.'/../lib/adminer/adminer-4.8.1-en.php';


function adminer_object() {
    include_once __DIR__.'/../lib/adminer/plugin.php';
    include_once __DIR__.'/../lib/adminer/plugin-login-password-less.php';
    return new AdminerPlugin(array(
        // TODO: inline the result of password_hash() so that the password is not visible in source codes
        new AdminerLoginPasswordLess(password_hash(DB_PASSWORD, PASSWORD_DEFAULT)),
    ));
}




