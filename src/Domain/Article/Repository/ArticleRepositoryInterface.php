<?php

declare(strict_types=1);

namespace App\Domain\Article\Repository;

use App\Domain\Article\Article;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

interface ArticleRepositoryInterface
{
    public function findAll(string $orderBy, string $orderDirection): Collection;

    public function findOneById(Uuid $id): ?Article;

    public function save(Article $article): void;

    public function delete(Article $article): void;
}
