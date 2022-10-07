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

    public function delete(Log $log): void
    {
        foreach ($this->erasers as $eraser) {
            $eraser->delete($log);
        }
    }

    public function deleteAlike(Log $log): void
    {
        foreach ($this->erasers as $eraser) {
            $eraser->deleteAlike($log);
        }
    }
}
