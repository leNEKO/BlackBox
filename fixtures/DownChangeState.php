<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\{
    Property,
    Set,
    Runner\Assert,
};

final class DownChangeState implements Property
{
    /**
     * @return Set<self>
     */
    public static function any(): Set
    {
        return Set\Elements::of(new self);
    }

    public function applicableTo(object $counter): bool
    {
        return $counter->current() > 0;
    }

    public function ensureHeldBy(Assert $assert, object $counter): object
    {
        $initial = $counter->current();
        $counter->down();
        $assert->number($initial)->greaterThan($counter->current());

        return $counter;
    }
}
