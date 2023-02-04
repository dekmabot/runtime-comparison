<?php

declare(strict_types=1);

namespace DragonCode\RuntimeComparison\Transformers;

class Times extends Base
{
    public function transform(array $data, ?int $roundPrecision): array
    {
        $items = [];

        foreach ($data as $name => $metrics) {
            foreach ($metrics as $metricName => $values) {
                foreach ($values as $iteration => $value) {
                    $items[$iteration]['#']                       = $iteration;
                    $items[$iteration][$name . ' ' . $metricName] = $this->round($value, $roundPrecision);
                }
            }
        }

        return array_values($items);
    }
}
