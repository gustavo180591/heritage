<?php

namespace App\Utils;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Util\XmlUtils;

class CustomXmlFileLoader extends FileLoader
{
    private $currentDir;

    public function __construct(FileLocatorInterface $locator)
    {
        parent::__construct($locator);
    }

    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);
        $this->setCurrentDir(\dirname($path));

        // Try to load the file with error suppression
        try {
            $dom = $this->parseFile($path);
        } catch (\Exception $e) {
            // If parsing fails, return an empty configuration
            return [];
        }
        
        return [];
    }

    public function supports($resource, $type = null): bool
    {
        return \is_string($resource) && 'xml' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    private function parseFile($file)
    {
        try {
            return XmlUtils::loadFile($file);
        } catch (\Exception $e) {
            // Return a simple DOMDocument to prevent errors
            $dom = new \DOMDocument();
            $dom->loadXML('<config></config>');
            return $dom;
        }
    }
}
