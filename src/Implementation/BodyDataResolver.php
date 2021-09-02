<?php

namespace Phox\Nebula\Http\Implementation;

use Phox\Nebula\Http\Notion\Interfaces\IContentResolver;
use Phox\Structures\Collection;

class BodyDataResolver
{
    public Collection $contentResolvers;

    public function __construct()
    {
        $this->contentResolvers = new Collection(IContentResolver::class);
    }

    public function resolve(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? null;

        if (is_null($contentType)) {
            return [];
        }

        $resolver = $this->getResolver($contentType);
        $unresolvedData = $this->getUnresolvedData();

        return $resolver?->resolve($unresolvedData) ?? [];
    }

    protected function getUnresolvedData(): string|bool
    {
        return file_get_contents('php://input');
    }

    protected function getResolver(string $contentType): ?IContentResolver
    {
        return $this->contentResolvers->tryGet($contentType);
    }
}