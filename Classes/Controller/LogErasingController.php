<?php

declare(strict_types=1);

namespace CoStack\Logs\Controller;

use CoStack\Logs\Domain\Model\Log;
use CoStack\Logs\Log\Eraser\EraserCollection;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;

class LogErasingController extends ActionController
{
    protected EraserCollection $eraserCollection;

    public function injectEraserCollection(EraserCollection $eraserCollection): void
    {
        $this->eraserCollection = $eraserCollection;
    }

    /**
     * @throws StopActionException
     */
    public function deleteAction(
        string $requestId,
        float $timeMicro,
        string $component,
        int $level,
        string $message
    ): void {
        $log = new Log($requestId, $timeMicro, $component, $level, $message, []);
        $this->eraserCollection->delete($log);
        $this->redirect('filter', 'LogReading');
    }

    /**
     * @throws StopActionException
     */
    public function deleteAlikeAction(string $component, int $level, string $message): void
    {
        $log = new Log('', 0.0, $component, $level, $message, []);
        $this->eraserCollection->deleteAlike($log);
        $this->redirect('filter', 'LogReading');
    }
}
