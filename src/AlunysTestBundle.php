<?php

namespace Alunys\TestBundle;

use Alunys\TestBundle\DependencyInjection\AlunysTestExtension;
use Alunys\TestBundle\DependencyInjection\DatabaseLoaderPluginPass;
use Alunys\TestBundle\DependencyInjection\DataFixturesPass;
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
