<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Exception;

trait WithTrait
{
    const JOINS = ['left', 'inner'];

    private static function getJoinAlias(string $field, ?string $alias = null): array
    {
        $parts = explode('.', $field);
        if ($alias) {
            $parts = [$alias, ...$parts];
        }
        $lastKey = array_key_last($parts);
        $target = $parts[$lastKey];
        unset($parts[$lastKey]);
        $parent = implode('_', $parts);
        return [$parent . '.' . $target, $parent . '_' . $target];
    }

    /**
     * @throws Exception
     */
    public function with(array $fields, string $join = 'left', ?QueryBuilder $qb = null): QueryBuilder
    {
        if (!($this instanceof EntityRepository)) {
            throw new Exception(WithTrait::class . ' can only be used for ' . EntityRepository::class);
        }

        if (!in_array($join, self::JOINS)) {
            throw new Exception(sprintf(
                '"%s" is not available, please use only: %s', $join, implode(', ', self::JOINS)
            ));
        }

        $alias = $qb?->getRootAliases()[0] ?? 'entity';

        $qb ??= $this->createQueryBuilder($alias);

        foreach ($fields as $field) {
            $joinAlias = self::getJoinAlias($field, $alias);
            $qb->{$join . 'Join'}($joinAlias[0], $joinAlias[1])
                ->addSelect($joinAlias[1]);
        }

        return $qb;
    }
}