<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

class AppTestKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', false);

        $this->removeCache();
    }

    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new \Alunys\SymfonyTestBundle\AlunysTestBundle(),
        ];
    }

    public function getCacheDir()
    {
        return __DIR__ . '/../../var/cache/' . $this->getEnvironment();
    }

    public function getLogDir()
    {
        return __DIR__ . '/../../var/logs' . $this->getEnvironment();
    }

    public function getVendorDir()
    {
        return $this->getProjectDir(). '/vendor';
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->setParameter('kernel.secret', 'foo');
        $loader->load(__DIR__ . '/config_test.yml');
    }

    protected function configureRoutes(\Symfony\Component\Routing\RouteCollectionBuilder $routes)
    {
    }

    protected function removeCache()
    {
        (new \Symfony\Component\Filesystem\Filesystem())->remove($this->getCacheDir());
    }

}
