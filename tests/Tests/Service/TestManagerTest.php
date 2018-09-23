<?php

namespace Alunys\SymfonyTestBundle\Tests\Sevice;

use Alunys\SymfonyTestBundle\Service\DatabaseLoaderPlugin\DatabaseLoaderPluginInterface;
use Alunys\SymfonyTestBundle\Service\TestManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ResettableContainerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\KernelInterface;

class TestManagerTest extends TestCase
{
    /**
     * @var TestManager
     */
    private $testManager;

    public function setUp()
    {
        parent::setUp();
        $this->testManager = new TestManager();
    }

    public function testGetKernel()
    {
        $this->assertInstanceOf(\AppTestKernel::class, $this->testManager->getKernel());
    }

    public function testGetClient()
    {
        $this->assertInstanceOf(Client::class, $this->testManager->getClient());
    }

    public function testGetContainer()
    {
        $this->assertInstanceOf(ContainerInterface::class, $this->testManager->getContainer());
    }

    public function testShutdownKernel()
    {
        // Method reset from container should be called
        $containerMock = $this->getMockBuilder(ResettableContainerInterface::class)->getMock();
        $containerMock->expects($this->once())->method('reset');

        // Method shutdown from kernel containing the container above should be called
        $kernelMock = $this->getMockBuilder(KernelInterface::class)->disableOriginalConstructor()->getMock();
        $kernelMock->expects($this->once())->method('shutdown');
        $kernelMock->method('getContainer')->willReturn($containerMock);

        // Set the mocked kernel for the "kernel" property from the testManager object
        $reflectionClass = new \ReflectionClass(TestManager::class);
        $kernelProperty = $reflectionClass->getProperty('kernel');
        $kernelProperty->setAccessible(true);
        $kernelProperty->setValue($this->testManager, $kernelMock);

        $this->testManager->shutdownKernel();
    }

    public function testloadDatabase()
    {
        // Stub for the DatabaseLoaderPluginLocator
        $databaseLoaderPluginLocator = new ServiceLocator(['database_loader_plugin_mock' => function () {
            $databaseLoaderPlugin = $this->getMockBuilder(DatabaseLoaderPluginInterface::class)->getMock();
            // Method loadDatabase from the DatabaseLoaderPluginInterface should be called
            $databaseLoaderPlugin->expects($this->once())->method('loadDatabase');

            return $databaseLoaderPlugin;
        }]);

        $this->testManager->getContainer()->set('alunys.symfony_test_bundle.service.database.database_loader_plugin_locator', $databaseLoaderPluginLocator);

        $this->testManager->loadDatabase('database_loader_plugin_mock');
    }
}
