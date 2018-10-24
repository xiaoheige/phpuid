<?php
echo "短ID测试:\n";
require_once './ShortUid.php';
$id = (new ShortUid)->generate(1, 0);
echo $id, "\n";
echo (new ShortUid)->get_mysqlid($id), "\n";
echo (new ShortUid)->get_machine($id), "\n";
echo "\n";

echo "长ID测试:\n";
require_once './LongUid.php';
$id = (new LongUid)->generate(0, 2);
echo $id, "\n";
echo (new LongUid)->get_time($id), "\n";
echo (new LongUid)->get_datacenter($id), "\n";
echo (new LongUid)->get_machine($id), "\n";

