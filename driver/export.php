<?php
require_once 'Template.class.php';
$baseDir = str_replace('\\', '/', dirname(__DIR__));
$temp = new Template($baseDir.'/view/', $baseDir.'/compiled/');
