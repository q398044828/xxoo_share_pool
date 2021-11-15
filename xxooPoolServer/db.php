<?php
require_once './medoo.php';


$db = new medoo([
    'database_type' => 'sqlite',
    'database_file' => DB_URL
]);
