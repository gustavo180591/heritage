<?php

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

require __DIR__.'/vendor/autoload.php';

$fs = new Filesystem();
$cacheDir = __DIR__.'/var/cache';

if ($fs->exists($cacheDir)) {
    $finder = new Finder();
    $finder->in($cacheDir)->notName('.gitkeep');
    
    $fs->remove($finder);
    echo "Cache cleared successfully!\n";
} else {
    echo "Cache directory not found.\n";
}
