<?php

namespace Alunys\TestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DatabaseLoaderPluginPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $databaseLoaderPluginLocatorDefinition = $container->getDefinition('Alunys\TestBundle\Service\Database\DatabaseLoaderPluginLocator');

        $pluginArguments = [];
        foreach ($container->findTaggedServiceIds('alunys.test.database_loader') as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['plugin_id'])) {
                    $tag['plugin_id'] = $serviceId;
                }
                $pluginArguments[$tag['plugin_id']] = new Reference($serviceId);
            }
        }

        $databaseLoaderPluginLocatorDefinition->addArgument($pluginArguments);
    }
}