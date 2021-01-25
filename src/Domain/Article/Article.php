<?php

declare(strict_types=1);

namespace App\Domain\Article;

use Symfony\Component\Uid\Uuid;

final class Article
{
    public Uuid $id;
    public string $title;
    public string $body;
    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $updatedAt;

    private function __construct(Uuid $id, string $title, string $body, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
        $this->createdAt = $createdAt;
        $this->updatedAt = $createdAt;
    }

    public static function create(Uuid $id, string $title, string $body, \DateTimeImmutable $createdAt): Article
    {
        return new self($id, $title, $body, $createdAt);
    }

    public function update(string $title, string $body, \DateTimeImmutable $updatedAt): void
    {
        $this->title = $title;
        $this->body = $body;
        $this->updatedAt = $updatedAt;
    }
}
