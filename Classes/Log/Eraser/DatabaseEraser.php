<?php

declare(strict_types=1);

namespace CoStack\Logs\Log\Eraser;

use CoStack\Logs\Domain\Model\Log;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Log\Writer\DatabaseWriter;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DatabaseEraser implements Eraser
{
    private string $table;
    private Connection $connection;

    public function __construct(DatabaseWriter $databaseWriter)
    {
        $this->table = $databaseWriter->getLogTable();
        $this->connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->table);
    }

    public static function getDefaultConfigForUniqueKeys(): array
    {
        return ['logTable' => 'sys_log'];
    }

    public function delete(Log $log): int
    {
        return $this->connection->delete($this->table, [
            'request_id' => $log->requestId,
            'time_micro' => $log->timeMicro,
            'component' => $log->component,
            'level' => $log->level,
            'message' => $log->message,
        ]);
    }

    public function deleteAlike(Log $log): int
    {
        return $this->connection->delete($this->table, [
            'component' => $log->component,
            'level' => $log->level,
            'message' => $log->message,
        ]);
    }
}
