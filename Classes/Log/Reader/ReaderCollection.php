<?php

declare(strict_types=1);

namespace CoStack\Logs\Log\Reader;

use CoStack\Logs\Domain\Model\Filter;
use CoStack\Logs\Domain\Model\Log;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function array_merge;
use function array_slice;
use function lcfirst;
use function strcmp;
use function usort;

class ReaderCollection
{
    /**
     * @param array<Reader> $readers
     */
    public function __construct(protected array $readers)
    {
    }

    /**
     * @return array<Log>
     */
    public function findByFilter(Filter $filter): array
    {
        $logs = [];
        foreach ($this->readers as $reader) {
            $logs[] = $reader->findByFilter($filter);
        }
        $logs = array_merge([], ...$logs);
        $orderField = lcfirst(GeneralUtility::underscoredToUpperCamelCase($filter->getOrderField()));

        $direction = Filter::SORTING_ASC === $filter->getOrderDirection() ? -1 : 1;

        usort(
            $logs,
            static fn(Log $left, Log $right) =>
                $direction * strcmp((string)$right->{$orderField}, (string)$left->{$orderField})
        );
        return array_slice($logs, 0, $filter->getLimit());
    }
}
