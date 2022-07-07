<?php

declare(strict_types=1);

namespace CoStack\Logs\Log\Eraser;

use CoStack\Logs\Domain\Model\Log;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConjunctionEraser implements EraserInterface
{
    /**
     * @var EraserInterface[]
     */
    protected array $eraser = [];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $configuration = null)
    {
        $eraserFactory = GeneralUtility::makeInstance(EraserFactory::class);
        $this->eraser = $eraserFactory->getErasersForWriters($configuration);
    }

    public function delete(Log $log): int
    {
        $deleted = 0;
        foreach ($this->eraser as $eraser) {
            $deleted += $eraser->delete($log);
        }
        return $deleted;
    }

    public function deleteAlike(Log $log): int
    {
        $deleted = 0;
        foreach ($this->eraser as $eraser) {
            $deleted += $eraser->deleteAlike($log);
        }
        return $deleted;
    }
}
