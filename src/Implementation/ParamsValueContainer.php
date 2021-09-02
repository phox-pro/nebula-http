<?php

namespace Phox\Nebula\Http\Implementation;

class ParamsValueContainer
{
    public function __construct(protected array $initialArray) {}

    public function get(string $key): mixed
    {
        return $this->initialArray[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->initialArray);
    }

    public function all(): array
    {
        return $this->initialArray;
    }
}