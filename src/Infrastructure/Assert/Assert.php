<?php

namespace App\Infrastructure\Assert;

final class Assert extends \Assert\Assert
{
    protected static $lazyAssertionExceptionClass = LazyAssertionException::class;
}
