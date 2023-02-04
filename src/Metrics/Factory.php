<?php

declare(strict_types=1);

namespace DragonCode\RuntimeComparison\Metrics;

use DragonCode\RuntimeComparison\Contracts\Transformer as TransformerContract;

class Factory
{
    public function time(): TransformerContract
    {
        return $this->resolve(Time::class);
    }

    public function memory(): TransformerContract
    {
        return $this->resolve(Memory::class);
    }

    protected function resolve(TransformerContract|string $transformer): TransformerContract
    {
        return (new $transformer());
    }
}
