<?php

declare(strict_types=1);

namespace App\Application\Command\Article;

use App\Domain\Article\Article;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use App\Infrastructure\Assert\Assert;
use Symfony\Component\Uid\Uuid;

final class CreateArticleCommandHandler
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function handle(CreateArticleCommand $command): Article
    {
        Assert::lazy()
            ->that($command->title, 'title')->notEmpty('Title must be filled in')
            ->that($command->body, 'body')->notEmpty('Body must be filled in')
            ->verifyNow();

        $article = Article::create(Uuid::v4(), $command->title, $command->body, new \DateTimeImmutable());
        $this->articleRepository->save($article);

        return $article;
    }
}
