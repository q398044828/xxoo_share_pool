<?php
require_once './medoo.php';
require_once './config.php';


$db = new medoo([
    'database_type' => 'sqlite',
    'database_file' => DB_URL
]);
