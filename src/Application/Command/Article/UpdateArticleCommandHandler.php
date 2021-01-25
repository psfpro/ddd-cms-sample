<?php

declare(strict_types=1);

namespace App\Application\Command\Article;

use App\Domain\Article\Article;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use App\Infrastructure\Assert\Assert;
use Symfony\Component\Uid\Uuid;

final class UpdateArticleCommandHandler
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function handle(UpdateArticleCommand $command): Article
    {
        Assert::lazy()
            ->that($command->id, 'id')->uuid('ID must be UUID')
            ->that($command->title, 'title')->notEmpty('Title must be filled in')
            ->that($command->body, 'body')->notEmpty('Body must be filled in')
            ->verifyNow();

        $article = $this->articleRepository->findOneById(Uuid::fromString($command->id));
        Assert::lazy()->that($article, 'id')->notEmpty('Article not found')->verifyNow();

        $article->update($command->title, $command->body, new \DateTimeImmutable());
        $this->articleRepository->save($article);

        return $article;
    }
}
