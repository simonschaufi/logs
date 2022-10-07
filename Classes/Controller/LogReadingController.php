<?php

declare(strict_types=1);

namespace CoStack\Logs\Controller;

use CoStack\Logs\Domain\Model\Filter;
use CoStack\Logs\Log\Reader\ReaderCollection;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;

class LogReadingController extends ActionController
{
    use ModuleTemplate;

    protected ReaderCollection $readerCollection;

    public function injectReaderCollection(ReaderCollection $readerCollection): void
    {
        $this->readerCollection = $readerCollection;
    }

    /**
     * @throws NoSuchArgumentException
     */
    protected function initializeFilterAction(): void
    {
        if ($this->request->hasArgument('filter')) {
            $filter = $this->request->getArgument('filter');
            $this->getBackendUser()->setAndSaveSessionData('tx_logs_filter', $filter);
        } else {
            $filter = $this->getBackendUser()->getSessionData('tx_logs_filter');
            if (null !== $filter) {
                $this->request->setArgument('filter', $filter);
                $this->arguments->getArgument('filter')->getPropertyMappingConfiguration()->allowAllProperties();
            }
        }
    }

    /**
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("filter")
     */
    public function filterAction(?Filter $filter = null): ResponseInterface
    {
        $filter = $filter ?? new Filter();

        $logs = $this->readerCollection->findByFilter($filter);

        $this->view->assign('filter', $filter);
        $this->view->assign('logs', $logs);

        return $this->htmlResponse();
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
