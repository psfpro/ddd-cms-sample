<?php

namespace App\Tests\Application\Command\Article;

use App\Application\Command\Article\DeleteArticleCommand;
use App\Application\Command\Article\DeleteArticleCommandHandler;
use App\Domain\Article\Article;
use App\Infrastructure\Assert\LazyAssertionException;
use App\Infrastructure\Persistence\Fake\ArticleRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class DeleteArticleCommandHandlerTest extends TestCase
{
    public function testHandle()
    {
        $repository = new ArticleRepository();
        $handler = new DeleteArticleCommandHandler($repository);
        $uuid = Uuid::v4();
        $article = Article::create($uuid, 'Before update title', 'Before update body', new \DateTimeImmutable());
        $repository->save($article);
        $this->assertCount(1, $repository->getData());

        try {
            $handler->handle(DeleteArticleCommand::fromArray([
                'id' => Uuid::v4()
            ]));
        } catch (LazyAssertionException $assertionException) {
            $this->assertEquals([
                'id' => 'Article not found',
            ], $assertionException->toArray());
        }
        $handler->handle(DeleteArticleCommand::fromArray([
            'id' => $uuid
        ]));

        $this->assertCount(0, $repository->getData());
    }
}
