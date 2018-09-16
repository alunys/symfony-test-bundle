<?php

namespace Alunys\SymfonyTestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DatabaseLoaderPluginPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $databaseLoaderPluginLocatorDefinition = $container->getDefinition('alunys.symfony_test_bundle.service.database.database_loader_plugin_locator');

        $pluginArguments = [];
        foreach ($container->findTaggedServiceIds('alunys.test.database_loader_plugin') as $serviceId => $tags) {
            $pluginArguments[$serviceId] = new Reference($serviceId);
        }

        $databaseLoaderPluginLocatorDefinition->addArgument($pluginArguments);
    }
}
