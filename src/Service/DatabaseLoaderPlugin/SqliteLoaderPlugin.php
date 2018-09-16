<?php

namespace Alunys\SymfonyTestBundle\Service\DatabaseLoaderPlugin;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use Doctrine\DBAL\Schema\SqliteSchemaManager;
use Doctrine\Common\DataFixtures\Loader as DataFixturesloader;

class SqliteLoaderPlugin implements DatabaseLoaderPluginInterface
{
    const DATABASE_TEMPLATE_NAME = 'database_template.sqlite';

    const FIXTURE_TAG = 'doctrine.fixture.orm';

    static $databaseCreated = false;

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var DataFixturesloader
     */
    protected $dataFixturesloader;

    public function __construct(ManagerRegistry $managerRegistry, DataFixturesloader $dataFixturesloader)
    {
        $this->managerRegistry = $managerRegistry;
        $this->dataFixturesloader = $dataFixturesloader;
    }

    public function loadDatabase(bool $forceRecreate = false)
    {
        if ($forceRecreate || !self::$databaseCreated) {
            $this->createTmpDatabase();
        } else {
            $this->checkConnectionPathParameter($params = $this->managerRegistry->getConnection()->getParams());

            // Replace current database by template database
            if (!copy($sourceFilePath = dirname($params['path'], 1) . '/' . self::DATABASE_TEMPLATE_NAME, $destinationFilePath = $params['path'])) {
                throw new \RuntimeException('Sqlite file "' . $sourceFilePath . '" could not be copied to "' . $destinationFilePath . '"');
            }
        }

        // Clear manager
        $this->managerRegistry->getManager()->clear();
    }

    protected function createTmpDatabase()
    {
        /* @var \Doctrine\DBAL\Connection $connection */
        $connection = $this->managerRegistry->getConnection();
        /* @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this->managerRegistry->getManager();

        if (!($connection->getDriver() instanceof Driver)) {
            throw new \LogicException('driver for doctrine connection must get an instance of "\Doctrine\DBAL\Driver\PDOSqlite\Driver", "' . get_class($connection->getDriver()) . '" given');
        }
        if (!($connection->getSchemaManager() instanceof SqliteSchemaManager)) {
            throw new \LogicException('schemaManager for doctrine connection must get an instance of "\Doctrine\DBAL\Schema\SqliteSchemaManager", "' . get_class($connection->getSchemaManager()) . '" given');
        }

        // Create current database
        $params = $connection->getParams();
        $this->checkConnectionPathParameter($params);

        if (is_file($params['path'])) {
            unlink($params['path']);
        }

        $connection->getSchemaManager()->createDatabase($params['path']);

        // Create tables from entities annotations
        $schemaTool = new SchemaTool($entityManager);
        try {
            $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
        } catch (\Exception $exception) {
            throw new \RuntimeException('Database could not be created', 500, $exception);
        }

        // Load fixtures
        $this->loadFixtures();

        // Make a copy of current database as template for reuse
        copy($params['path'], dirname($params['path'], 1) . '/' . self::DATABASE_TEMPLATE_NAME);

        self::$databaseCreated = true;
    }

    protected function loadFixtures()
    {
        $entityManager = $this->managerRegistry->getManager();

        foreach ($this->dataFixturesloader->getFixtures() as $fixture) {
            $fixture->load($entityManager);
        }
        $entityManager->clear();
    }

    protected function checkConnectionPathParameter(array $params)
    {
        if (!isset($params['path'])) {
            throw new \LogicException('Database "path" parameter is not configured, current configuration : ' . print_r($params, true));
        }
        if (!preg_match('/\.sqlite$/', $params['path'])) {
            throw new \LogicException('Sqlite file name must be given for the "path" parameter in configuration, "' . $params['path'] . '" given');
        }
    }
}
