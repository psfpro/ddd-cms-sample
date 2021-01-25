<?php

declare(strict_types=1);

namespace App\Application\Command\Article;

use App\Application\Command\DataTransferObject;

final class DeleteArticleCommand extends DataTransferObject
{
    public ?string $id = null;
}
