<?php
echo "PHP is working! Time: " . date('Y-m-d H:i:s');
echo "\nPHP Version: " . PHP_VERSION;
echo "\nWorking Directory: " . getcwd();
echo "\nDocument Root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'undefined';