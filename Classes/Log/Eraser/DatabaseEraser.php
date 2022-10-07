<?php

declare(strict_types=1);

namespace CoStack\Logs\Log\Eraser;

use CoStack\Logs\Domain\Model\Log;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DatabaseEraser implements EraserInterface
{
    protected string $table = 'sys_log';
    protected Connection $connection;

    public function __construct(?array $configuration = null)
    {
        if (null !== $configuration && isset($configuration['logTable'])) {
            $this->table = $configuration['logTable'];
        } else {
            $this->table = 'sys_log';
        }
        $this->connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->table);
    }

    public function delete(Log $log): void
    {
        $this->connection->delete($this->table, $this->getWhere($log));
    }

    protected function getWhere(Log $log): array
    {
        return [
            'request_id' => $log->getRequestId(),
            'time_micro' => $log->getTimeMicro(),
            'component' => $log->getComponent(),
            'level' => $log->getLevel(),
            'message' => $log->getMessage(),
        ];
    }

    public function deleteAlike(Log $log): void
    {
        $this->connection->delete($this->table, $this->getWhereAlike($log));
    }

    protected function getWhereAlike(Log $log): array
    {
        return [
            'component' => $log->getComponent(),
            'level' => $log->getLevel(),
            'message' => $log->getMessage(),
        ];
    }
}
