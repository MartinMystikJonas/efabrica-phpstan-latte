<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Collector\ValueObject;

use PHPStan\ShouldNotHappenException;

/**
 * @phpstan-type CollectedResolvedNodeParam null|string|int|array<string, null|string|int}
 * @phpstan-type CollectedResolvedNodeArray array{resolver: string, params: array<string, string>}
 */
final class CollectedResolvedNode
{
    private string $resolver;

    /** @var array<string, CollectedResolvedNodeParam>> */
    protected array $params;

  /**
   * @param string $resolver
   * @param array<string, CollectedResolvedNodeParam> $params
   */
    final public function __construct(string $resolver, array $params)
    {
        $this->resolver = $resolver;
        $this->params = $params;
    }

    public function getResolver(): string
    {
        return $this->resolver;
    }

  /**
   * @return array<string|CollectedResolvedNodeParam>
   */
    public function getParams(): array
    {
        return $this->params;
    }

  /**
   * @return CollectedResolvedNodeParam
   */
    public function getParam(string $name): mixed
    {
        if (!array_key_exists($name, $this->params)) {
            throw new ShouldNotHappenException("Unkwnown CollectedResolvedNode parameter '$name'");
        }
        return $this->params[$name];
    }

    /**
     * @phpstan-return CollectedResolvedNodeArray
     */
    public function toArray(): array
    {
        return [
            'resolver' => $this->resolver,
            'params' => $this->params,
        ];
    }

    /**
     * @param CollectedResolvedNodeArray $item
     */
    public static function fromArray(array $item): self
    {
        return new CollectedResolvedNode($item['resolver'], $item['params']);
    }
}
