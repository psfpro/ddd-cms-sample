<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Extractor;

use IteratorIterator;
use Traversable;

final class ExtractingIterator extends IteratorIterator
{
    private ExtractorInterface $extractor;

    public function __construct(Traversable $data, ExtractorInterface $extractor)
    {
        $this->extractor = $extractor;
        parent::__construct($data);
    }

    /**
     * @return array
     */
    public function current()
    {
        $currentValue = parent::current();

        return $this->extractor->extract($currentValue);
    }
}
