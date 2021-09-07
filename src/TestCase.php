<?php

namespace Phox\Nebula\Http;

use Phox\Nebula\Atom\Implementation\ProvidersContainer;
use Phox\Nebula\Atom\TestCase as AtomTestCase;

class TestCase extends AtomTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $providersContainer = $this->container()->get(ProvidersContainer::class);
        $providersContainer->addProvider(new HttpProvider());
    }
}