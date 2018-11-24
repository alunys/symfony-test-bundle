<?php

namespace Alunys\SymfonyTestBundle\Service;

use Alunys\SymfonyTestBundle\Service\DatabaseLoaderPlugin\DatabaseLoaderPluginInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ResettableContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

class TestManager
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * Get the kernel dedicated to the test environment
     *
     * @return KernelInterface
     */
    public function getKernel(): KernelInterface
    {
        if (!($this->kernel instanceof KernelInterface)) {
            $this->kernel = $this->createKernel();
        }
        return $this->kernel;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->getKernel()->getContainer();
    }

    /**
     * Creates a Client.
     *
     * @param array $server
     * @return Client
     */
    public function getClient(array $server = array()): Client
    {
        $client = $this->getContainer()->get('test.client');
        $client->setServerParameters($server);

        return $client;
    }

    /**
     * Shuts the kernel down.
     */
    public function shutdownKernel()
    {
        if (!($this->kernel instanceof KernelInterface)) {
            throw new \LogicException('Kernel can not be shut downed if it has not been created');
        }

        // Close containers
        $container = $this->getContainer();
        $this->getKernel()->shutdown();
        if ($container instanceof ResettableContainerInterface) {
            $container->reset();
        }
    }

    /**
     * @param string $databaseLoaderpluginIdentifier
     * @param bool $forceRecreate
     * @return DatabaseLoaderPluginInterface
     */
    public function loadDatabase($databaseLoaderpluginIdentifier = 'Alunys\SymfonyTestBundle\Service\DatabaseLoaderPlugin\SqliteLoaderPlugin', bool $forceRecreate = false): DatabaseLoaderPluginInterface
    {
        $databaseLoaderPluginLocator = $this->getContainer()->get('alunys.symfony_test_bundle.service.database.database_loader_plugin_locator');

        $databaseLoaderpluginIdentifier = $databaseLoaderPluginLocator->get($databaseLoaderpluginIdentifier);
        $databaseLoaderpluginIdentifier->loadDatabase($forceRecreate);

        return $databaseLoaderpluginIdentifier;
    }

    /**
     * Boots the Kernel.
     *
     * @param array $options
     * @return KernelInterface
     */
    protected function createKernel(): KernelInterface
    {
        if (isset($_ENV['APP_DEBUG'])) {
            $debug = $_ENV['APP_DEBUG'];
        } elseif (isset($_SERVER['APP_DEBUG'])) {
            $debug = $_SERVER['APP_DEBUG'];
        } else {
            $debug = true;
        }

        // Create new kernel for test
        /* @var KernelInterface $kernel */
        $kernelClass = $this->getKernelClass();

        $kernel = new $kernelClass('test', $debug);

        // Boot kernel
        $kernel->boot();
        return $kernel;
    }

    /**
     * Attempts to guess the kernel location.
     *
     * When the Kernel is located, the file is required.
     *
     * @throws \RuntimeException
     * @return string
     */
    protected function getKernelClass(): string
    {
        if (isset($_SERVER['KERNEL_CLASS']) || isset($_ENV['KERNEL_CLASS'])) {
            $class = isset($_ENV['KERNEL_CLASS']) ? $_ENV['KERNEL_CLASS'] : $_SERVER['KERNEL_CLASS'];
            if (!class_exists($class)) {
                throw new \RuntimeException(sprintf('Class "%s" doesn\'t exist or cannot be autoloaded. Check that the KERNEL_CLASS value in phpunit.xml matches the fully-qualified class name of your Kernel or override the %s::createKernel() method.', $class, self::class));
            }

            return $class;
        }

        if (isset($_SERVER['KERNEL_DIR']) || isset($_ENV['KERNEL_DIR'])) {
            $dir = isset($_ENV['KERNEL_DIR']) ? $_ENV['KERNEL_DIR'] : $_SERVER['KERNEL_DIR'];

            if (!is_dir($dir)) {
                $phpUnitDir = $this->getPhpUnitXmlDir();
                if (is_dir("$phpUnitDir/$dir")) {
                    $dir = "$phpUnitDir/$dir";
                }
            }
        } else {
            $dir = $this->getPhpUnitXmlDir();
        }

        $finder = new Finder();
        $finder->name('*Kernel.php')->depth(0)->in($dir);
        $results = iterator_to_array($finder);
        if (!count($results)) {
            throw new \RuntimeException('Either set KERNEL_DIR in your phpunit.xml according to https://symfony.com/doc/current/book/testing.html#your-first-functional-test or override the WebTestCase::createKernel() method.');
        }

        $file = current($results);
        $class = $file->getBasename('.php');

        require_once $file;

        return $class;
    }

    /**
     * Finds the directory where the phpunit.xml(.dist) is stored.
     *
     * If you run tests with the PHPUnit CLI tool, everything will work as expected.
     * If not, override this method in your test classes.
     *
     * @return string The directory where phpunit.xml(.dist) is stored
     *
     * @throws \RuntimeException
     */
    protected function getPhpUnitXmlDir()
    {
        if (!isset($_SERVER['argv']) || false === strpos($_SERVER['argv'][0], 'phpunit')) {
            throw new \RuntimeException('You must override the KernelTestCase::createKernel() method.');
        }

        $dir = $this->getPhpUnitCliConfigArgument();
        if (null === $dir &&
            (is_file(getcwd() . DIRECTORY_SEPARATOR . 'phpunit.xml') ||
                is_file(getcwd() . DIRECTORY_SEPARATOR . 'phpunit.xml.dist'))) {
            $dir = getcwd();
        }

        // Can't continue
        if (null === $dir) {
            throw new \RuntimeException('Unable to guess the Kernel directory.');
        }

        if (!is_dir($dir)) {
            $dir = dirname($dir);
        }

        return $dir;
    }

    /**
     * Finds the value of the CLI configuration option.
     *
     * PHPUnit will use the last configuration argument on the command line, so this only returns
     * the last configuration argument.
     *
     * @return string The value of the PHPUnit CLI configuration option
     */
    private function getPhpUnitCliConfigArgument()
    {
        $dir = null;
        $reversedArgs = array_reverse($_SERVER['argv']);
        foreach ($reversedArgs as $argIndex => $testArg) {
            if (preg_match('/^-[^ \-]*c$/', $testArg) || '--configuration' === $testArg) {
                $dir = realpath($reversedArgs[$argIndex - 1]);
                break;
            } elseif (0 === strpos($testArg, '--configuration=')) {
                $argPath = substr($testArg, strlen('--configuration='));
                $dir = realpath($argPath);
                break;
            } elseif (0 === strpos($testArg, '-c')) {
                $argPath = substr($testArg, strlen('-c'));
                $dir = realpath($argPath);
                break;
            }
        }

        return $dir;
    }
}
