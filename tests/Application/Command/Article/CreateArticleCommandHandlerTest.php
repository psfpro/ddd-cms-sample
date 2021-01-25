<?php

namespace App\Tests\Application\Command\Article;

use App\Application\Command\Article\CreateArticleCommand;
use App\Application\Command\Article\CreateArticleCommandHandler;
use App\Infrastructure\Assert\LazyAssertionException;
use App\Infrastructure\Persistence\Fake\ArticleRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class CreateArticleCommandHandlerTest extends TestCase
{
    public function testHandle()
    {
        $repository = new ArticleRepository();
        $handler = new CreateArticleCommandHandler($repository);

        try {
            $handler->handle(CreateArticleCommand::fromArray([]));
        } catch (LazyAssertionException $assertionException) {
            $this->assertEquals([
                'title' => 'Title must be filled in',
                'body' => 'Body must be filled in',
            ], $assertionException->toArray());
        }

        $handler->handle(CreateArticleCommand::fromArray([
            'id' => Uuid::v4(),
            'title' => 'Article title',
            'body' => 'Article body'
        ]));

        $this->assertCount(1, $repository->getData());
    }
}
