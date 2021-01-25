<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use IteratorAggregate;
use RuntimeException;
use App\Infrastructure\Persistence\Doctrine\Extractor\ExtractingIterator;
use App\Infrastructure\Persistence\Doctrine\Extractor\ExtractorInterface;

final class DataProvider implements IteratorAggregate
{
    private Collection $collection;
    private int $page;
    private int $perPage;
    private ?ExtractorInterface $extractor;

    public function __construct(Collection $collection, ExtractorInterface $extractor = null)
    {
        $this->collection = $collection;
        $this->extractor = $extractor;

        $this->page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $this->perPage = filter_input(INPUT_GET, 'per-page', FILTER_VALIDATE_INT) ?: 20;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page)
    {
        $this->page = $page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(int $limit)
    {
        $this->perPage = $limit;
    }

    public function getPageCount(): int
    {
        return intval(ceil($this->count() / $this->getPerPage()));
    }

    public function getCollection(): Collection
    {
        return $this->collection;
    }

    public function getIterator(): Collection
    {
        $offset = ($this->getPage() - 1) * $this->getPerPage();
        $length = $this->getPerPage();

        return new ArrayCollection($this->collection->slice($offset, $length));
    }

    public function toArray(): array
    {
        return iterator_to_array($this->getIterator(), false);
    }

    public function extract(): array
    {
        if ($this->extractor) {
            return iterator_to_array(new ExtractingIterator($this->getIterator(), $this->extractor), false);
        } else {
            throw new RuntimeException('Need configure Extractor');
        }
    }

    public function count(): int
    {
        return $this->collection->count();
    }
}
