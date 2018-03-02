<?php
/**
 * Date: 18-3-2
 * Time: 下午4:13
 */
include './Backup.php';
require __DIR__ . '/vendor/autoload.php';

$backup = new Backup();
$backup->backup();