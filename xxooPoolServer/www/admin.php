<?php
require_once __DIR__ . '/../config.php';

if (!DB_OTHER_MANAGER) {
    require_once __DIR__ . '/../admin/admin.php';
}
