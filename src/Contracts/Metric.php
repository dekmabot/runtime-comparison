<?php

declare(strict_types=1);

namespace DragonCode\RuntimeComparison\Contracts;

interface Metric
{
    public function start();
    public function finish();
}
