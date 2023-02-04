<?php

declare(strict_types=1);

namespace DragonCode\RuntimeComparison\Metrics;

class Memory extends Base
{
    protected int $startsAt;

    public function start()
    {
        memory_reset_peak_usage();
        $this->startsAt = $this->current();
    }

    public function finish(): int
    {
        return $this->current() - $this->startsAt;
    }

    protected function current(): int
    {
        return memory_get_peak_usage(true);
    }
}
