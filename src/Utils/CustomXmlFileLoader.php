<?php

namespace App\Utils;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Util\XmlUtils;

class CustomXmlFileLoader extends FileLoader
{
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

        $this->parseImports($dom, $path);
        
        return [];
    }

    public function supports($resource, $type = null)
    {
        return \is_string($resource) && 'xml' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    protected function parseFile($file)
    {
        try {
            $dom = XmlUtils::loadFile($file);
            $this->validate($dom, $file);
        } catch (\Exception $e) {
            // Return a simple DOMDocument to prevent errors
            $dom = new \DOMDocument();
            $dom->loadXML('<config></config>');
        }

        return $dom;
    }

    protected function validate(\DOMDocument $dom, $file)
    {
        // Skip validation
        return true;
    }

    protected function parseImports(\DOMDocument $xml, $file)
    {
        // Skip imports
        return;
    }
}
