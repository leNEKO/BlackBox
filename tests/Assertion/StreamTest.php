<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\Stream,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\TimeContinuum\ElapsedPeriodInterface;
use Innmind\Immutable\{
    Map,
    Stream as IStream,
};
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Assertion::class, new Stream('int'));
    }

    public function testInvokation()
    {
        $assert = new Stream('int');

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(IStream::of('int'), $this->createMock(ElapsedPeriodInterface::class)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result('foo', $this->createMock(ElapsedPeriodInterface::class)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not a stream', (string) $report->failure()->message());

        $report = $assert(
            $this->createMock(OperatingSystem::class),
            new ScenarioReport,
            new Result(IStream::of('mixed'), $this->createMock(ElapsedPeriodInterface::class)),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame('Not a stream of type <int>, got <mixed>', (string) $report->failure()->message());
    }
}
