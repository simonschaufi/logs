<?php

declare(strict_types=1);

namespace CoStack\Logs\Controller;

use CoStack\Logs\Domain\Model\Log;
use CoStack\Logs\Log\Eraser\EraserCollection;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class LogErasingController extends ActionController
{
    protected EraserCollection $eraserCollection;

    public function injectEraserCollection(EraserCollection $eraserCollection): void
    {
        $this->eraserCollection = $eraserCollection;
    }

    /**
     * @noinspection PhpUnused Plugin action called by Extbase
     */
    public function deleteAction(
        string $requestId,
        float $timeMicro,
        string $component,
        int $level,
        string $message
    ): RedirectResponse {
        $log = new Log($requestId, $timeMicro, $component, $level, $message, []);
        $this->eraserCollection->delete($log);
        $uri = $this->uriBuilder->uriFor('filter', [], 'LogReading');
        return new RedirectResponse($uri);
    }

    /**
     * @noinspection PhpUnused Plugin action called by Extbase
     */
    public function deleteAlikeAction(string $component, int $level, string $message): RedirectResponse
    {
        $log = new Log('', 0.0, $component, $level, $message, []);
        $this->eraserCollection->deleteAlike($log);
        $uri = $this->uriBuilder->uriFor('filter', [], 'LogReading');
        return new RedirectResponse($uri);
    }
}
