# alunys/symfony-test-bundle

[![buddy pipeline](https://app.buddy.works/raphaelprmntr/symfony-test-bundle/pipelines/pipeline/134921/badge.svg?token=b9a1c1227208abcd0108ba1cc3059674a5910015681e565fd91b78fbdac67ea4 "buddy pipeline")](https://app.buddy.works/raphaelprmntr/symfony-test-bundle/pipelines/pipeline/134921)

## Usage

Use `Alunys\SymfonyTestBundle\Service\TestManager` in tests to retrieve function helpers :
* Load a database for test :
```php
use Alunys\SymfonyTestBundle\Service\TestManager;
(new TestManager())->loadDatabase();
```
The `Alunys\SymfonyTestBundle\Service\DatabaseLoaderPlugin\SqliteLoaderPlugin` is used by default when calling the method `loadDatabase` : \
1/ It creates a SQLite database from doctrine entities, it depends on the doctrine configuration. 
```yaml
# example of doctrine connection configuration for test
doctrine:
     dbal:
         driver: 'pdo_sqlite'
         path: '%kernel.cache_dir%/db.sqlite'
         charset: 'UTF8'
```
2/  The database is populated from doctrine fixtures found in the project.

## Testing the bundle inside a docker container for dev:
* run : `docker-compose run --rm php vendor/bin/phpunit`
