<?php

declare(strict_types=1);

namespace CoStack\LogsDev\Logs\Reader;

use CoStack\Logs\Domain\Model\Filter;
use CoStack\Logs\Log\Reader\Reader;

class RedisReader implements Reader
{
    public function __construct(?array $configuration = null)
    {
    }

    public static function getDefaultConfigForUniqueKeys(): array
    {
        return [];
    }

    public function findByFilter(Filter $filter): array
    {
        return [];
    }
}
