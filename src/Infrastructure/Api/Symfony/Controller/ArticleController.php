<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Symfony\Controller;

use App\Application\Command\Article\CreateArticleCommand;
use App\Application\Command\Article\CreateArticleCommandHandler;
use App\Application\Command\Article\DeleteArticleCommand;
use App\Application\Command\Article\DeleteArticleCommandHandler;
use App\Application\Command\Article\UpdateArticleCommand;
use App\Application\Command\Article\UpdateArticleCommandHandler;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use App\Infrastructure\Api\Symfony\Extractor\ArticleExtractor;
use App\Infrastructure\Assert\LazyAssertionException;
use App\Infrastructure\Persistence\Doctrine\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ArticleController
{
    private Serializer $serializer;
    private ArticleExtractor $extractor;

    public function __construct()
    {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new XmlEncoder(), new JsonEncoder()]);
        $this->extractor = new ArticleExtractor();
    }

    public function index(ArticleRepositoryInterface $articleRepository, Request $request): Response
    {
        $articles = $articleRepository->findAll(
            $request->query->getAlpha('orderBy'),
            $request->query->getAlpha('orderDirection'),
        );
        $dataProvider = new DataProvider($articles, $this->extractor);

        return new Response($this->serializer->serialize([
            'page' => $dataProvider->getPage(),
            'pageCount' => $dataProvider->getPageCount(),
            'perPage' => $dataProvider->getPerPage(),
            'data' => $dataProvider->extract(),
        ], $this->contentFormat($request)));
    }

    public function create(CreateArticleCommandHandler $createArticleCommandHandler, Request $request): Response
    {
        if (!$this->authorization($request)) {
            return new Response($this->serializer->serialize([
                'message' => 'Authentication Required'
            ], $this->contentFormat($request)), Response::HTTP_UNAUTHORIZED);
        }

        $parsedBody = json_decode($request->getContent(), true);
        try {
            $article = $createArticleCommandHandler->handle(CreateArticleCommand::fromArray($parsedBody));
        } catch (LazyAssertionException $exception) {
            return new Response($this->serializer->serialize([
                'data' => $exception->toArray(),
            ], $this->contentFormat($request)), Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        return new Response($this->serializer->serialize([
            'data' => $this->extractor->extract($article)
        ], $this->contentFormat($request)));
    }

    public function update(UpdateArticleCommandHandler $updateArticleCommandHandler, Request $request): Response
    {
        if (!$this->authorization($request)) {
            return new Response($this->serializer->serialize([
                'message' => 'Authentication Required'
            ], $this->contentFormat($request)), Response::HTTP_UNAUTHORIZED);
        }

        $parsedBody = json_decode($request->getContent(), true);
        try {
            $article = $updateArticleCommandHandler->handle(UpdateArticleCommand::fromArray($parsedBody));
        } catch (LazyAssertionException $exception) {
            return new Response($this->serializer->serialize([
                'data' => $exception->toArray(),
            ], $this->contentFormat($request)), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new Response($this->serializer->serialize([
            'data' => $this->extractor->extract($article),
        ], $this->contentFormat($request)));
    }

    public function delete(DeleteArticleCommandHandler $deleteArticleCommandHandler, Request $request): Response
    {
        if (!$this->authorization($request)) {
            return new Response($this->serializer->serialize([
                'message' => 'Authentication Required'
            ], $this->contentFormat($request)), Response::HTTP_UNAUTHORIZED);
        }

        $parsedBody = json_decode($request->getContent(), true);
        try {
            $deleteArticleCommandHandler->handle(DeleteArticleCommand::fromArray($parsedBody));
        } catch (LazyAssertionException $exception) {
            return new Response($this->serializer->serialize([
                'data' => $exception->toArray(),
            ], $this->contentFormat($request)), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new Response($this->serializer->serialize([

        ], $this->contentFormat($request)));
    }

    private function contentFormat(Request $request): string
    {
        $type = current($request->getAcceptableContentTypes());
        switch ($type) {
            case 'application/xml':
                return 'xml';
            default:
                return 'json';
        }
    }

    private function authorization(Request $request): bool
    {
        $token = $request->headers->get('Authorization');


        return $token === 'Bearer special-key';
    }
}
