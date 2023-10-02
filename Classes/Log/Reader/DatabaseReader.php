<?php

declare(strict_types=1);

namespace CoStack\Logs\Log\Reader;

use CoStack\Logs\Domain\Model\Filter;
use CoStack\Logs\Domain\Model\Log;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use PDO;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\Writer\DatabaseWriter;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function array_map;
use function json_decode;
use function strlen;
use function substr;

class DatabaseReader implements Reader
{
    protected array $selectFields = [
        Log::FIELD_REQUEST_ID,
        Log::FIELD_TIME_MICRO,
        Log::FIELD_COMPONENT,
        Log::FIELD_LEVEL,
        Log::FIELD_MESSAGE,
        Log::FIELD_DATA,
    ];
    protected string $table = '';
    protected ?Connection $connection = null;

    public function __construct(DatabaseWriter $writer)
    {
        $this->table = $writer->getLogTable();
        $this->connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->table);
    }

    public static function getDefaultConfigForUniqueKeys(): array
    {
        return ['logTable' => 'sys_log'];
    }

    /**
     * @throws Exception
     */
    public function findByFilter(Filter $filter): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->getRestrictions()->removeAll();

        $quote = function (string $field) use ($query): string {
            return $query->quoteIdentifier($field);
        };
        $selectFields = array_map($quote, $this->selectFields);

        if (!$filter->isFullMessage()) {
            $selectFields[4] = 'CONCAT(LEFT(' . $selectFields[4] . ' , 120), "...") as message';
        }
        if (!$filter->isShowData()) {
            $selectFields[5] = '"- {}"';
        }
        $query->selectLiteral(...$selectFields);

        $query->from($this->table);

        $logLevel = LogLevel::normalizeLevel($filter->getLevel());
        $query->where($query->expr()->lte(Log::FIELD_LEVEL, $query->createNamedParameter($logLevel, PDO::PARAM_INT)));
        $query->andWhere($query->expr()->isNotNull(Log::FIELD_MESSAGE));

        $requestId = $filter->getRequestId();
        if (!empty($requestId)) {
            /* @see \TYPO3\CMS\Core\Core\RequestId::__construct for requestId string length */
            if (13 === strlen($requestId)) {
                $constraint = $query->expr()->eq(Log::FIELD_REQUEST_ID, $query->createNamedParameter($requestId));
            } else {
                $constraint = $query->expr()->like(Log::FIELD_REQUEST_ID, $query->createNamedParameter("%$requestId%"));
            }
            $query->andWhere($constraint);
        }
        $fromTime = $filter->getFromTime();
        if ($fromTime !== null) {
            $query->andWhere($query->expr()->gte(Log::FIELD_TIME_MICRO, $query->createNamedParameter($fromTime)));
        }
        $toTime = $filter->getToTime();
        if ($toTime !== null) {
            // Add +1 to the timestamp to ignore additional microseconds when comparing. UX stuff, you know ;)
            $query->andWhere($query->expr()->lte(Log::FIELD_TIME_MICRO, $query->createNamedParameter($toTime + 1)));
        }
        $component = $filter->getComponent();
        if (!empty($component)) {
            $query->andWhere($query->expr()->like(Log::FIELD_COMPONENT, $query->createNamedParameter("%$component%")));
        }

        $query->orderBy($filter->getOrderField(), $filter->getOrderDirection());
        $limit = $filter->getLimit();
        if ($limit > 0) {
            $query->setMaxResults($limit);
        }
        $result = $query->executeQuery();

        return $this->fetchLogsFromResult($result);
    }

    /**
     * @return Log[]
     * @throws Exception
     */
    protected function fetchLogsFromResult(Result $result): array
    {
        $logs = [];

        while (($row = $result->fetchNumeric())) {
            $row[5] = $row[5] === '' ? null : json_decode(substr($row[5], 2), true);
            $logs[] = new Log($row[0], $row[1], $row[2], (int)$row[3], $row[4], $row[5]);
        }
        return $logs;
    }
}
