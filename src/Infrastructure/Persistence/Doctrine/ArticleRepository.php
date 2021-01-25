<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Article\Article;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

final class ArticleRepository extends DoctrineRepository implements ArticleRepositoryInterface
{
    protected function getEntityClassName(): string
    {
        return Article::class;
    }

    public function findAll(string $orderBy, string $orderDirection): Collection
    {
        $qb = $this->entityRepository->createQueryBuilder('a');
        if (in_array($orderBy, ['title', 'createdAt', 'updatedAt']) && in_array($orderDirection, ['asc', 'desc'])) {
            $qb->orderBy('a.' . $orderBy, $orderDirection);
        }

        return new QueryBuilderCollection($qb);
    }

    public function findOneById(Uuid $id): ?Article
    {
        /** @var Article|null $article */
        $article = $this->entityRepository->findOneBy(['id' => $id]);

        return $article;
    }

    public function save(Article $article): void
    {
        $this->entityManager->persist($article);
        $this->entityManager->flush();
    }

    public function delete(Article $article): void
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }
}
