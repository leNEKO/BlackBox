<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof;

use Innmind\BlackBox\{
    Set,
    Runner\Proof,
    Runner\Assert,
    Property as Concrete,
};

final class Property implements Proof
{
    /** @var class-string<Concrete> */
    private string $property;
    /** @var ?non-empty-string */
    private ?string $name;
    /** @var Set<object> */
    private Set $systemUnderTest;
    /** @var list<\UnitEnum> */
    private array $tags;

    /**
     * @param class-string<Concrete> $property
     * @param ?non-empty-string $name
     * @param Set<object> $systemUnderTest
     * @param list<\UnitEnum> $tags
     */
    private function __construct(
        string $property,
        ?string $name,
        Set $systemUnderTest,
        array $tags,
    ) {
        $this->property = $property;
        $this->name = $name;
        $this->systemUnderTest = $systemUnderTest;
        $this->tags = $tags;
    }

    /**
     * @param class-string<Concrete> $property
     * @param Set<object> $systemUnderTest
     */
    public static function of(
        string $property,
        Set $systemUnderTest,
    ): self {
        return new self($property, null, $systemUnderTest, []);
    }

    /**
     * @psalm-mutation-free
     *
     * @param non-empty-string $name
     */
    public function named(string $name): self
    {
        return new self(
            $this->property,
            $name,
            $this->systemUnderTest,
            $this->tags,
        );
    }

    public function name(): Name
    {
        return Name::of(match ($this->name) {
            null => $this->property,
            default => \sprintf(
                '%s(%s)',
                $this->property,
                $this->name,
            ),
        });
    }

    /**
     * @psalm-mutation-free
     * @no-named-arguments
     */
    public function tag(\UnitEnum ...$tags): self
    {
        return new self(
            $this->property,
            $this->name,
            $this->systemUnderTest,
            [...$this->tags, ...$tags],
        );
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function scenarii(int $count): Set
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @psalm-suppress InvalidArgument
         * @psalm-suppress MixedArgument
         * @var Set<Scenario>
         */
        return Set\Composite::immutable(
            Scenario\Property::of(...),
            ([$this->property, 'any'])(),
            $this->systemUnderTest,
        )->take($count);
    }
}
