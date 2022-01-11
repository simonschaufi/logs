<?php

declare(strict_types=1);

namespace CoStack\Logs\Log\Reader;

use CoStack\Logs\Domain\Model\Filter;
use CoStack\Logs\Domain\Model\Log;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

use function array_merge;
use function array_slice;
use function strcmp;
use function usort;

class ConjunctionReader implements ReaderInterface
{
    /** @var ReaderInterface[] */
    protected array $readers = [];

    public function __construct(?array $configuration = null)
    {
        $readerFactory = GeneralUtility::makeInstance(ReaderFactory::class);
        $this->readers = $readerFactory->getReadersForWriters($configuration);
    }

    public static function getDefaultConfigForUniqueKeys(): array
    {
        return [];
    }

    /**
     * @return Log[]
     */
    public function findByFilter(Filter $filter): array
    {
        $logs = [];
        foreach ($this->readers as $reader) {
            $logs = array_merge($logs, $reader->findByFilter($filter));
        }
        $orderField = GeneralUtility::underscoredToUpperCamelCase($filter->getOrderField());
        $direction = Filter::SORTING_ASC === $filter->getOrderDirection() ? -1 : 1;
        usort(
            $logs,
            static function ($left, $right) use ($orderField, $direction) {
                return $direction
                       * strcmp(
                           (string)ObjectAccess::getProperty($right, $orderField),
                           (string)ObjectAccess::getProperty($left, $orderField)
                       );
            }
        );
        return array_slice($logs, 0, $filter->getLimit());
    }

    /**
     * @return ReaderInterface[]
     */
    public function getReaders(): array
    {
        return $this->readers;
    }

    /**
     * @param ReaderInterface[] $readers
     */
    public function setReaders(array $readers): void
    {
        $this->readers = $readers;
    }

    /**
     * @param ReaderInterface[] $readers
     */
    public function addReaders(array $readers): void
    {
        foreach ($readers as $reader) {
            $this->addReader($reader);
        }
    }

    public function addReader(ReaderInterface $reader): void
    {
        $this->readers[] = $reader;
    }
}
