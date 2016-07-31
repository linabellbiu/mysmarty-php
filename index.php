<?php
include_once __DIR__.'/driver/export.php';
$temp->assign('test','hello');
$temp->display('test');   //填写模板文件名
