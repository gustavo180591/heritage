<?php

echo "Checking PHP Extensions:\n";
$extensions = ['dom', 'SimpleXML', 'pdo', 'pdo_mysql'];

foreach ($extensions as $ext) {
    echo "- $ext: " . (extension_loaded($ext) ? '✓ Loaded' : '✗ Not loaded') . "\n";
}

echo "\nPHP Version: " . PHP_VERSION . "\n";

// Check for duplicate extension loading
echo "\nChecking for duplicate extension loading in php.ini files:\n";
$configFiles = [
    php_ini_loaded_file(),
    '/etc/php/7.4/cli/conf.d/20-simplexml.ini',
    '/etc/php/7.4/cli/conf.d/20-dom.ini'
];

foreach ($configFiles as $file) {
    if (file_exists($file)) {
        echo "\n$file:\n";
        echo "----------------------------------------\n";
        echo file_get_contents($file) . "\n";
    } else {
        echo "\n$file: Not found\n";
    }
}
