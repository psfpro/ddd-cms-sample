<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Fake;

use App\Domain\Article\Article;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

final class ArticleRepository implements ArticleRepositoryInterface
{
    private array $data = [];

    public function getData(): array
    {
        return $this->data;
    }

    public function findAll(string $orderBy, string $orderDirection): Collection
    {
        return new ArrayCollection($this->data);
    }

    public function findOneById(Uuid $id): ?Article
    {
        foreach ($this->data as $k => $v) {
            if ($k === (string)$id) {
                return $v;
            }
        }

        return null;
    }

    public function save(Article $article): void
    {
        $this->data[(string)$article->id] = $article;
    }

    public function delete(Article $article): void
    {
        unset($this->data[(string)$article->id]);
    }
}
