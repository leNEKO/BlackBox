<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Composite,
    Set\FromGenerator,
    Set,
    Set\Value,
};

class CompositeTest extends TestCase
{
    private $set;

    public function setUp(): void
    {
        $this->set = new Composite(
            function(string ...$args) {
                return implode('', $args);
            },
            FromGenerator::of(function() {
                yield 'e';
                yield 'f';
            }),
            FromGenerator::of(function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of(function() {
                yield 'c';
                yield 'd';
            }),
        );
    }

    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, $this->set);
    }

    public function testOf()
    {
        $this->assertInstanceOf(
            Composite::class,
            Composite::of(
                function() {},
                FromGenerator::of(function() {
                    yield 'e';
                    yield 'f';
                }),
                FromGenerator::of(function() {
                    yield 'a';
                    yield 'b';
                }),
                FromGenerator::of(function() {
                    yield 'c';
                    yield 'd';
                }),
            ),
        );
    }

    public function testTake()
    {
        $values = $this->unwrap($this->set->take(2)->values());

        $this->assertSame(
            [
                'eac',
                'ead',
            ],
            $values,
        );
    }

    public function testFilter()
    {
        $values = $this
            ->set
            ->filter(static function(string $value): bool {
                return $value[0] === 'e';
            });

        $this->assertSame(
            [
                'eac',
                'ead',
                'ebc',
                'ebd',
            ],
            $this->unwrap($values->values()),
        );
    }

    public function testReduce()
    {
        $values = $this->unwrap($this->set->values());

        $this->assertSame(
            [
                'eac',
                'ead',
                'ebc',
                'ebd',
                'fac',
                'fad',
                'fbc',
                'fbd',
            ],
            $values,
        );
    }

    public function testValues()
    {
        $this->assertInstanceOf(\Generator::class, $this->set->values());
        $this->assertCount(8, $this->unwrap($this->set->values()));

        foreach ($this->set->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }
}
