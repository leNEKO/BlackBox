<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\Same,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class SameTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Assertion::class, new Same(42));
    }

    public function testInvokation()
    {
        $assert = new Same(1);

        $report = $assert(
            new ScenarioReport,
            new Result(1),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $assert = new Same(1);

        $report = $assert(
            new ScenarioReport,
            new Result(2),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not same', (string) $report->failure()->message());
    }
}