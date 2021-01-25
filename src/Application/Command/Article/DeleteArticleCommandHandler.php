<?php

declare(strict_types=1);

namespace App\Application\Command\Article;

use App\Domain\Article\Article;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use App\Infrastructure\Assert\Assert;
use Symfony\Component\Uid\Uuid;

final class DeleteArticleCommandHandler
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function handle(DeleteArticleCommand $command): void
    {
        $article = $this->articleRepository->findOneById(Uuid::fromString($command->id));
        Assert::lazy()->that($article, 'id')->notEmpty('Article not found')->verifyNow();

        $this->articleRepository->delete($article);
    }
}
