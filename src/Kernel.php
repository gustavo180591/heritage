<?php

namespace App;

use App\DependencyInjection\Compiler\ReplaceXmlFileLoaderPass;
use App\DependencyInjection\EnvVarProcessor;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);
        
        // Add our custom compiler pass
        $container->addCompilerPass(new ReplaceXmlFileLoaderPass());
        
        // Add our custom environment variable loader
        if ($container->has('cache_warmer')) {
            $container->getDefinition('cache_warmer')
                ->addMethodCall('add', [new Reference(EnvVarProcessor::class)]);
        }
    }
    
    protected function getKernelParameters()
    {
        $parameters = parent::getKernelParameters();
        $parameters['env(SYMFONY_DOCTRINE_DISABLE_XML_VALIDATION)'] = '1';
        return $parameters;
    }
}
