<?php

declare(strict_types=1);

namespace CoStack\Logs\Log\Eraser;

use CoStack\Logs\Domain\Model\Log;

class EraserCollection
{
    /**
     * @var array<Eraser>
     */
    protected array $erasers = [];

    /**
     * @param array<Eraser> $erasers
     */
    public function __construct(array $erasers)
    {
        $this->erasers = $erasers;
    }

    public function delete(Log $log): int
    {
        $count = 0;
        foreach ($this->erasers as $eraser) {
            $count += $eraser->delete($log);
        }
        return $count;
    }

    public function deleteAlike(Log $log): int
    {
        $count = 0;
        foreach ($this->erasers as $eraser) {
            $count += $eraser->deleteAlike($log);
        }
        return $count;
    }
}
