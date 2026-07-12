<?php
require_once __DIR__ . '/../app/Core/Database.php';

$db = \App\Core\Database::getInstance();
$db->getConnection()->exec("UPDATE specialized_fields SET image_name = 'biochem.png' WHERE image_name = 'biochemistry.png'");
echo "Database updated successfully.\n";
