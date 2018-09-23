<?php

namespace Alunys\SymfonyTestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DataFixturesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $dataFixturesLoaderDefinition = $container->getDefinition('alunys.symfony_test_bundle.service.database.data_fixtures_loader');
        $fixturesId = $container->findTaggedServiceIds('doctrine.fixture.orm');


        foreach ($fixturesId as $fixtureId => $tag) {
            $dataFixturesLoaderDefinition->addMethodCall('addFixture', [new Reference($fixtureId)]);
        }
    }
}
