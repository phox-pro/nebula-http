<?php

namespace Phox\Nebula\Http\Implementation;

use Phox\Nebula\Atom\Implementation\Functions;

class Request
{
    protected ParamsValueContainer $values;

    public function __construct(protected array $server)
    {
        $this->init();
    }

    public function getValues(): ParamsValueContainer
    {
        return $this->values;
    }

    public function getServerValue(string $key): mixed
    {
        return $this->server[$key] ?? null;
    }

    protected function init()
    {
        $this->setParams();
    }

    protected function setParams()
    {
        $bodyDataResolver = Functions::container()->get(BodyDataResolver::class);

        $this->values = new ParamsValueContainer(array_merge(
            $bodyDataResolver->resolve(),
            $_REQUEST,
        ));
    }
}