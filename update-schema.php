<?php

use App\Entity\Asset;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/vendor/autoload.php';

// Load environment variables
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

// Get the entity manager
$kernel = new App\Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();
$entityManager = $container->get('doctrine')->getManager();

// Get the schema tool
$schemaTool = new SchemaTool($entityManager);
$metadata = $entityManager->getMetadataFactory()->getAllMetadata();

// Create the schema SQL
$sqlList = $schemaTool->getUpdateSchemaSql($metadata, true);

if (empty($sqlList)) {
    echo "No database schema changes detected.\n";
    exit(0);
}

echo "The following SQL will be executed:\n";
foreach ($sqlList as $sql) {
    echo "- " . $sql . "\n";
}

// Ask for confirmation
echo "\nDo you want to execute these SQL statements? (y/n) ";
$handle = fopen('php://stdin', 'r');
$line = fgets($handle);
if (trim($line) !== 'y') {
    echo "Aborted.\n";
    exit(1);
}

// Execute the SQL
echo "Updating database schema...\n";
$connection = $entityManager->getConnection();
$connection->beginTransaction();

try {
    foreach ($sqlList as $sql) {
        $connection->executeStatement($sql);
    }
    $connection->commit();
    echo "Database schema updated successfully!\n";
} catch (\Exception $e) {
    $connection->rollBack();
    echo "Error updating database schema: " . $e->getMessage() . "\n";
    exit(1);
}
