<?php

declare(strict_types=1);

namespace DragonCode\RuntimeComparison;

use DragonCode\RuntimeComparison\Contracts\Metric as MetricContract;
use DragonCode\RuntimeComparison\Exceptions\ValueIsNotCallableException;
use DragonCode\RuntimeComparison\Metrics\Factory as MetricsFactory;
use DragonCode\RuntimeComparison\Metrics\Memory;
use DragonCode\RuntimeComparison\Metrics\Time;
use DragonCode\RuntimeComparison\Services\Runner;
use DragonCode\RuntimeComparison\Services\View;
use DragonCode\RuntimeComparison\Transformers\Transformer;
use ReflectionClass;
use Symfony\Component\Console\Helper\ProgressBar as ProgressBarService;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class Comparator
{
    protected View $view;

    protected int $iterations = 10;

    protected array $metrics = [
        Time::class,
    ];

    protected bool $withData = true;

    protected array $result = [];

    public function __construct(
        protected Runner         $runner = new Runner(),
        protected Transformer    $transformer = new Transformer(),
        protected MetricsFactory $metricsFactory = new MetricsFactory(),
    ) {
        $this->view = new View(new SymfonyStyle(
            new ArgvInput(),
            new ConsoleOutput()
        ));
    }

    public function iterations(int $count): self
    {
        $this->iterations = max(1, $count);

        return $this;
    }

    public function roundPrecision(?int $precision): self
    {
        $this->transformer->setRoundPrecision($precision);

        return $this;
    }

    public function withoutData(): self
    {
        $this->withData = false;

        return $this;
    }

    public function testMemory(): self
    {
        $this->metrics = [Memory::class];

        return $this;
    }

    public function compare(array|callable ...$callbacks): void
    {
        $values = is_array($callbacks[0]) ? $callbacks[0] : func_get_args();

        $this->withProgress($values, $this->metrics, $this->stepsCount($values));
        $this->show();
    }

    protected function withProgress(array $callbacks, array $metrics, int $count): void
    {
        $bar = $this->view->progressBar()->create($count);

        $this->each($callbacks, $metrics, $bar);

        $bar->finish();
        $this->view->emptyLine(2);
    }

    protected function stepsCount(array $callbacks): int
    {
        return count($callbacks) * $this->iterations;
    }

    protected function each(array $callbacks, array $metrics, ProgressBarService $progressBar): void
    {
        foreach ($callbacks as $name => $callback) {
            $this->validate($callback);

            $metricsList = [];
            /** @var MetricContract|string $metric */
            foreach ($metrics as $metricClass) {
                $metricsList[] = new $metricClass();
            }

            for ($i = 1; $i <= $this->iterations; ++$i) {

                /** @var MetricContract $metric */
                foreach ($metricsList as $metric) {
                    $metric->start();
                }

                $this->run($callback);
                $progressBar->advance();

                /** @var MetricContract $metric */
                foreach ($metricsList as $metric) {
                    $metricName = strtolower((new ReflectionClass($metric))->getShortName());
                    $this->push($name, $metricName, $i, $metric->finish());
                }
            }
        }
    }

    protected function run(callable $callback): void
    {
        $this->runner->call($callback);
    }

    protected function push(mixed $name, string $metricName, int $iteration, float|int $value): void
    {
        $this->result[$name][$metricName][$iteration] = $value;
    }

    protected function show(): void
    {
        $table = $this->withData() ? $this->transformer->forTime($this->result) : [];

        $stats  = $this->transformer->forStats($this->result);
        $winner = $this->transformer->forWinners($stats);

        $this->view->table($this->transformer->merge($table, $stats, $winner));
    }

    protected function withData(): bool
    {
        return $this->withData && $this->iterations <= 1000;
    }

    protected function validate(mixed $callback): void
    {
        if (! is_callable($callback)) {
            throw new ValueIsNotCallableException(gettype($callback));
        }
    }
}
