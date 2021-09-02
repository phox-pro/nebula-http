<?php

namespace Phox\Nebula\Http\Notion\Interfaces;

interface IContentResolver
{
    public function resolve(?string $data): array;
}