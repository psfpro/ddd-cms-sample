<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Extractor;

interface ExtractorInterface
{
    public function extract(object $object): array;
}
