<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\EnvVarLoaderInterface;

class EnvVarProcessor implements EnvVarLoaderInterface
{
    public function loadEnvVars(): array
    {
        return [
            'SYMFONY_DOCTRINE_DISABLE_XML_VALIDATION' => '1',
        ];
    }
}
