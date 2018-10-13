<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Given\Scenario,
    When\Result,
};

final class When
{
    private $test;

    public function __construct(callable $test)
    {
        // the wrapping is to make sure there is no _$this_ inside the callable;
        $this->test = \Closure::fromCallable($test)->bindTo(new class {});
    }

    public function __invoke(Scenario $scenario): Result
    {
        try {
            $result = ($this->test)($scenario);
        } catch (\Throwable $e) {
            $result = $e;
        } finally {
            return new Result($result);
        }
    }
}
