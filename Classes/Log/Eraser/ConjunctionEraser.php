<?php

declare(strict_types=1);

namespace CoStack\Logs\Log\Eraser;

use CoStack\Logs\Domain\Model\Log;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConjunctionEraser implements EraserInterface
{
    /** @var EraserInterface[] */
    protected array $eraser = [];

    public function __construct(?array $configuration = null)
    {
        $eraserFactory = GeneralUtility::makeInstance(EraserFactory::class);
        $this->eraser = $eraserFactory->getErasersForWriters($configuration);
    }

    public function delete(Log $log): void
    {
        foreach ($this->eraser as $eraser) {
            $eraser->delete($log);
        }
    }

    public function deleteAlike(Log $log): void
    {
        foreach ($this->eraser as $eraser) {
            $eraser->deleteAlike($log);
        }
    }
}
