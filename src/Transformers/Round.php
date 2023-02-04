<?php

declare(strict_types=1);

namespace DragonCode\RuntimeComparison\Transformers;

class Round extends Base
{
    public function __construct(
        protected ?int $precision
    )
    {
    }

    public function transform(array $data): array
    {
        $items = [];

        foreach ($data as $name => $values) {
            foreach ($values as $iteration => $time) {
                $items[$name][$iteration] = is_float($time)
                    ? $this->round($time)
                    : $time;
            }
        }

        return array_values($items);
    }

    public function round(int|float|string $time): int|float|string
    {
        return sprintf('%.' . $this->precision . 'f', floatval($time));
    }
}
