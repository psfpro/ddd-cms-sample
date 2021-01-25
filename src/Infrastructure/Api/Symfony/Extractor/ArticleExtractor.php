<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Symfony\Extractor;

use App\Domain\Article\Article;
use App\Infrastructure\Persistence\Doctrine\Extractor\ExtractorInterface;

final class ArticleExtractor implements ExtractorInterface
{
    public function extract(object $object): array
    {
        assert($object instanceof Article);

        return [
            'id' => (string)$object->id,
            'title' => $object->title,
            'body' => $object->body,
            'createdAt' => $object->createdAt->format(DATE_W3C),
            'updatedAt' => $object->updatedAt->format(DATE_W3C),
        ];
    }
}
