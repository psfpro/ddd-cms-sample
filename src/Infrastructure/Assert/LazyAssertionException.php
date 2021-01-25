<?php

namespace App\Infrastructure\Assert;

final class LazyAssertionException extends \Assert\LazyAssertionException
{
    public function toArray(): array
    {
        $errors = [];
        foreach ($this->getErrorExceptions() as $exception) {
            $errors[$exception->getPropertyPath()] = $exception->getMessage();
        }

        return $errors;
    }
}
