# alunys/symfony-test-bundle

[![buddy pipeline](https://app.buddy.works/raphaelprmntr/symfony-test-bundle/pipelines/pipeline/134921/badge.svg?token=b9a1c1227208abcd0108ba1cc3059674a5910015681e565fd91b78fbdac67ea4 "buddy pipeline")](https://app.buddy.works/raphaelprmntr/symfony-test-bundle/pipelines/pipeline/134921)

## Usage

Use ``Alunys\SymfonyTestBundle\Service\TestManager`` in tests to retrieve function helpers. \
The ``Alunys\SymfonyTestBundle\Service\DatabaseLoaderPlugin\SqliteLoaderPlugin`` is used by default to create a SQLite database from doctrine entities. The database created is populated from doctrine fixtures found in the project.

### Symfony commands
The bundle adds the commands in the project :
* ``alunys:symfony-test-bundle:phpunit`` : run phpunit

### Testing the bundle in standalone inside a docker container :
* run : `docker-compose run php-symfony-test-bundle vendor/bin/phpunit`
