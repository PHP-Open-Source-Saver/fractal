<?php


namespace PHPOpenSourceSaver\Fractal\Test\Stub;

use PHPOpenSourceSaver\Fractal\Serializer\ArraySerializer;

class ArraySerializerWithNull extends ArraySerializer
{
    public function null(): ?array
    {
        return null;
    }
}
