<?php

declare(strict_types=1);

namespace DragonCode\RuntimeComparison\Metrics;

class Time extends Base
{
    protected float $startsAt;

    public function start()
    {
        $this->startsAt = $this->current();
    }

    public function finish(): float
    {
        return $this->current() - $this->startsAt;
    }

    protected function current(): float
    {
        return microtime(true);
    }
}
