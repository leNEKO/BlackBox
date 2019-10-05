<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\{
    Composite\Matrix,
    Composite\Combination,
    FromGenerator,
};
use PHPUnit\Framework\TestCase;

class MatrixTest extends TestCase
{
    public function testInterface()
    {
        $matrix = new Matrix(
            FromGenerator::of(function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of(function() {
                yield new Combination('c');
                yield new Combination('d');
            })
        );

        $this->assertSame(
            [
                ['a', 'c'],
                ['a', 'd'],
                ['b', 'c'],
                ['b', 'd'],
            ],
            $this->toArray($matrix)
        );
    }

    public function testDot()
    {
        $matrix = new Matrix(
            FromGenerator::of(function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of(function() {
                yield new Combination('c');
                yield new Combination('d');
            })
        );
        $matrix2 = $matrix->dot(FromGenerator::of(
            function() {
                yield 'e';
                yield 'f';
            }
        ));

        $this->assertInstanceOf(Matrix::class, $matrix2);
        $this->assertNotSame($matrix, $matrix2);
        $this->assertSame(
            [
                ['a', 'c'],
                ['a', 'd'],
                ['b', 'c'],
                ['b', 'd'],
            ],
            $this->toArray($matrix)
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
            $this->toArray($matrix2)
        );
    }

    public function toArray(Matrix $matrix): array
    {
        return \array_map(
            function($combination) {
                return $combination->toArray();
            },
            \iterator_to_array($matrix->values())
        );
    }
}
