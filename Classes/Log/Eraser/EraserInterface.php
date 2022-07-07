<?php

declare(strict_types=1);

namespace CoStack\Logs\Log\Eraser;

use CoStack\Logs\Domain\Model\Log;

interface EraserInterface
{
    public function __construct(array $configuration = null);

    public function delete(Log $log): int;

    /**
     * Deletes all log entries with the same component, message and level (ignoring the request ID, micro time and data)
     */
    public function deleteAlike(Log $log): int;
}
