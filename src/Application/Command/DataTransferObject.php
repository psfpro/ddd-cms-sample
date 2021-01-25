<?php

declare(strict_types=1);

namespace App\Application\Command;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class DataTransferObject
{
    /**
     * @param array $data
     * @return DataTransferObject|static
     */
    public static function fromArray(array $data): DataTransferObject
    {
        $serializer = new Serializer([new PropertyNormalizer(null, new CamelCaseToSnakeCaseNameConverter())]);
        return $serializer->denormalize($data, static::class);
    }
}
