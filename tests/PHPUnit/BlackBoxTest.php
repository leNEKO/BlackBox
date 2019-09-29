<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};
use PHPUnit\Framework\TestCase;

class BlackBoxTest extends TestCase
{
    public function testTrait()
    {
        $class = new class() {
            use BlackBox;

            public function assert(): int
            {
                $called = 0;
                $this
                    ->forAll(Set\Integers::of('a'))
                    ->then(static function() use (&$called) {
                        ++$called;
                    });

                return $called;
            }
        };

        $this->assertSame(100, $class->assert());
    }
}
