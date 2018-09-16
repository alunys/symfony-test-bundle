<?php

namespace Alunys\SymfonyTestBundle\Service\DatabaseLoaderPlugin;

interface DatabaseLoaderPluginInterface
{
    /**
     * Load a cleaned database for tests
     */
    public function loadDatabase(bool $forceRecreate = false);
}
