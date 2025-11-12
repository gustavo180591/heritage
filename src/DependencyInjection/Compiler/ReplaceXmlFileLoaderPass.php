<?php

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

class ReplaceXmlFileLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Only replace the XML file loader in the dev environment
        if ('dev' !== $container->getParameter('kernel.environment')) {
            return;
        }

        // Replace the XML file loader with our custom one
        if ($container->hasDefinition('file_locator')) {
            // Define our custom XML file loader
            $container->register('app.file_loader.xml', 'App\\Utils\\CustomXmlFileLoader')
                ->addArgument(new Reference('file_locator'));

            // Replace the XML file loader in routing
            if ($container->hasDefinition('routing.loader.xml')) {
                $container->getDefinition('routing.loader.xml')
                    ->setClass('App\\Utils\\CustomXmlFileLoader')
                    ->replaceArgument(0, new Reference('app.file_loader.xml'));
            }

            if ($container->hasDefinition('config.resource.self_checking_resource_checker')) {
                $container->getDefinition('config.resource.self_checking_resource_checker')
                    ->addMethodCall('addCheck', [new Reference('app.file_loader.xml')]);
            }
        }
    }
}
