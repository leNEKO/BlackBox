<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\NaturalNumbersExceptZero,
    Set,
};
use PHPUnit\Framework\TestCase;

class NaturalNumbersExceptZeroTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new NaturalNumbersExceptZero
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(NaturalNumbersExceptZero::class, NaturalNumbersExceptZero::of());
    }

    public function testByDefault100IntegersAreGenerated()
    {
        $values = \iterator_to_array(NaturalNumbersExceptZero::of()->values());

        $this->assertCount(100, $values);

        foreach ($values as $value) {
            $this->assertTrue($value > 0);
        }
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $integers = NaturalNumbersExceptZero::of();
        $even = $integers->filter(static function(int $int): bool {
            return $int % 2 === 0;
        });

        $this->assertInstanceOf(NaturalNumbersExceptZero::class, $even);
        $this->assertNotSame($integers, $even);
        $hasOddInteger = \array_reduce(
            \iterator_to_array($integers->values()),
            static function(bool $hasOddInteger, int $value): bool {
                return $hasOddInteger || $value % 2 === 1;
            },
            false
        );
        $this->assertTrue($hasOddInteger);

        $hasOddInteger = \array_reduce(
            \iterator_to_array($even->values()),
            static function(bool $hasOddInteger, int $value): bool {
                return $hasOddInteger || $value % 2 === 1;
            },
            false
        );
        $this->assertFalse($hasOddInteger);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = NaturalNumbersExceptZero::of();
        $b = $a->take(50);

        $this->assertInstanceOf(NaturalNumbersExceptZero::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, \iterator_to_array($a->values()));
        $this->assertCount(50, \iterator_to_array($b->values()));
    }

    public function testValues()
    {
        $a = NaturalNumbersExceptZero::of();

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, \iterator_to_array($a->values()));
    }
}