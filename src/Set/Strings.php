<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @implements Set<string>
 */
final class Strings implements Set
{
    private int $maxLength;
    private int $size;
    private \Closure $predicate;

    public function __construct(int $maxLength = 128)
    {
        $this->maxLength = $maxLength;
        $this->size = 100;
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public static function any(int $maxLength = 128): self
    {
        return new self($maxLength);
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->size = $size;

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        $previous = $this->predicate;
        $self = clone $this;
        /**
         * @psalm-suppress MissingClosureParamType
         */
        $self->predicate = static function($value) use ($previous, $predicate): bool {
            if (!$previous($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function values(): \Generator
    {
        $iterations = 0;

        do {
            $value = '';

            foreach (range(1, \random_int(2, $this->maxLength)) as $_) {
                $value .= \chr(\random_int(33, 126));
            }

            if (!($this->predicate)($value)) {
                continue ;
            }

            yield Value::immutable($value);
            ++$iterations;
        } while ($iterations < $this->size);
    }
}
