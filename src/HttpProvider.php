<?php

namespace Phox\Nebula\Http;

use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Implementation\States\InitState;
use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\Atom\Notion\Interfaces\IStateContainer;
use Phox\Nebula\Http\Implementation\BodyDataResolver;
use Phox\Nebula\Http\Implementation\ContentResolvers\JsonResolver;
use Phox\Nebula\Http\Implementation\Request;
use Phox\Nebula\Http\Implementation\States\HttpState;

class HttpProvider extends Provider
{
    public function __invoke(IStateContainer $stateContainer, IDependencyInjection $dependencyInjection): void
    {
        $bodyResolver = new BodyDataResolver();

        $this->registerContentResolvers($bodyResolver);
        $dependencyInjection->singleton($bodyResolver);

        $httpState = new HttpState();

        $httpState->listen(fn(IDependencyInjection $dependencyInjection) => $this->onHttpState($dependencyInjection));
        $stateContainer->addAfter($httpState, InitState::class);
    }

    private function onHttpState(IDependencyInjection $dependencyInjection): void
    {
        $dependencyInjection->singleton(new Request($_SERVER));
    }

    private function registerContentResolvers(BodyDataResolver $bodyDataResolver): void
    {
        $bodyDataResolver->contentResolvers->collect([
            'application/json' => new JsonResolver(),
        ]);
    }
}