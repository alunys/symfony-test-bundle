<?php

namespace Alunys\SymfonyTestBundle;

use Alunys\SymfonyTestBundle\DependencyInjection\AlunysTestExtension;
use Alunys\SymfonyTestBundle\DependencyInjection\DatabaseLoaderPluginPass;
use Alunys\SymfonyTestBundle\DependencyInjection\DataFixturesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AlunysTestBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new DataFixturesPass());
        $container->addCompilerPass(new DatabaseLoaderPluginPass());
    }

    public function getContainerExtension()
    {
        return new AlunysTestExtension();
    }
}
