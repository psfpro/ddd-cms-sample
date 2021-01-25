<?php

namespace App\Tests\Application\Command\Article;

use App\Application\Command\Article\UpdateArticleCommand;
use App\Application\Command\Article\UpdateArticleCommandHandler;
use App\Domain\Article\Article;
use App\Infrastructure\Assert\LazyAssertionException;
use App\Infrastructure\Persistence\Fake\ArticleRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class UpdateArticleCommandHandlerTest extends TestCase
{
    public function testHandle()
    {
        $repository = new ArticleRepository();
        $handler = new UpdateArticleCommandHandler($repository);
        $uuid = Uuid::v4();
        $article = Article::create($uuid, 'Before update title', 'Before update body', new \DateTimeImmutable());
        $repository->save($article);
        $this->assertCount(1, $repository->getData());

        try {
            $handler->handle(UpdateArticleCommand::fromArray([]));
        } catch (LazyAssertionException $assertionException) {
            $this->assertEquals([
                'title' => 'Title must be filled in',
                'body' => 'Body must be filled in',
                'id' => 'ID must be UUID',
            ], $assertionException->toArray());
        }

        try {
            $handler->handle(UpdateArticleCommand::fromArray([
                'id' => Uuid::v4(),
                'title' => 'Article title',
                'body' => 'Article body'
            ]));
        } catch (LazyAssertionException $assertionException) {
            $this->assertEquals([
                'id' => 'Article not found',
            ], $assertionException->toArray());
        }

        $handler->handle(UpdateArticleCommand::fromArray([
            'id' => $uuid,
            'title' => 'Article title',
            'body' => 'Article body'
        ]));

        $this->assertCount(1, $repository->getData());

        $afterUpdate = $repository->findOneById($uuid);
        $this->assertEquals(
            [
                'title' => 'Article title',
                'body' => 'Article body'
            ],
            [
                'title' => $afterUpdate->title,
                'body' => $afterUpdate->body,
            ]
        );
    }
}
