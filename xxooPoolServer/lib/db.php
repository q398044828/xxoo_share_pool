<?php
require_once __DIR__ . './medoo.php';


$db = new medoo([
    'database_type' => 'sqlite',
    'database_file' => DB_URL
]);
