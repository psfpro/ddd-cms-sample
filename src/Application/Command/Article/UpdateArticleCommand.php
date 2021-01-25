<?php

declare(strict_types=1);

namespace App\Application\Command\Article;

use App\Application\Command\DataTransferObject;

final class UpdateArticleCommand extends DataTransferObject
{
    public ?string $id = null;
    public ?string $title = null;
    public ?string $body = null;
}
