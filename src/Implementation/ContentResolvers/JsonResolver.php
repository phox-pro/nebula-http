<?php

namespace Phox\Nebula\Http\Implementation\ContentResolvers;

use Phox\Nebula\Http\Notion\Interfaces\IContentResolver;

class JsonResolver implements IContentResolver
{
    public function resolve(?string $data): array
    {
        return json_decode($data, true) ?? [];
    }
}