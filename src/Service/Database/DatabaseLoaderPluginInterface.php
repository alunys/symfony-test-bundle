<?php

namespace Alunys\TestBundle\Service\Database;

interface DatabaseLoaderPluginInterface
{
    /**
     * Load a cleaned database for tests
     */
    public function loadDatabase(bool $forceRecreate = false);
}