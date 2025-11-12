<?php

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ReplaceXmlFileLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Replace the XML file loader with our custom one
        if ($container->hasDefinition('file_locator')) {
            $container->register('app.file_loader.xml', 'App\Utils\CustomXmlFileLoader')
                ->addArgument(new Reference('file_locator'));
            
            if ($container->hasDefinition('routing.loader.xml')) {
                $container->getDefinition('routing.loader.xml')
                    ->replaceArgument(0, new Reference('app.file_loader.xml'));
            }
            
            if ($container->hasDefinition('config.resource.self_checking_resource_checker')) {
                $container->getDefinition('config.resource.self_checking_resource_checker')
                    ->addMethodCall('addCheck', [new Reference('app.file_loader.xml')]);
            }
        }
    }
}
