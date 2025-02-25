<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\{
    Set\Composite\Matrix,
    Set\Composite\Combination,
    Set\FromGenerator,
    Set\Value,
    Random,
};
use PHPUnit\Framework\TestCase;

class MatrixTest extends TestCase
{
    public function testInterface()
    {
        $matrix = new Matrix(
            FromGenerator::of(static function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of(static function() {
                yield new Combination(Value::immutable('c'));
                yield new Combination(Value::immutable('d'));
            }),
        );

        $this->assertSame(
            [
                ['a', 'c'],
                ['a', 'd'],
                ['b', 'c'],
                ['b', 'd'],
            ],
            $this->toArray($matrix),
        );
    }

    public function testDot()
    {
        $matrix = new Matrix(
            FromGenerator::of(static function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of(static function() {
                yield new Combination(Value::immutable('c'));
                yield new Combination(Value::immutable('d'));
            }),
        );
        $matrix2 = $matrix->dot(FromGenerator::of(static function() {
            yield 'e';
            yield 'f';
        }));

        $this->assertInstanceOf(Matrix::class, $matrix2);
        $this->assertNotSame($matrix, $matrix2);
        $this->assertSame(
            [
                ['a', 'c'],
                ['a', 'd'],
                ['b', 'c'],
                ['b', 'd'],
            ],
            $this->toArray($matrix),
        );
        $this->assertSame(
            [
                ['e', 'a', 'c'],
                ['e', 'a', 'd'],
                ['e', 'b', 'c'],
                ['e', 'b', 'd'],
                ['f', 'a', 'c'],
                ['f', 'a', 'd'],
                ['f', 'b', 'c'],
                ['f', 'b', 'd'],
            ],
            $this->toArray($matrix2),
        );
    }

    public function toArray(Matrix $matrix): array
    {
        return \array_map(
            static function($combination) {
                return $combination->unwrap();
            },
            \iterator_to_array($matrix->values(Random::mersenneTwister)),
        );
    }
}
