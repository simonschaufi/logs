<?php

declare(strict_types=1);

namespace CoStack\Logs\Controller;

use CoStack\Logs\Domain\Model\Filter;
use CoStack\Logs\Domain\Model\Log;
use CoStack\Logs\Log\Eraser\ConjunctionEraser;
use CoStack\Logs\Log\Reader\ConjunctionReader;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;

class LogController extends ActionController
{
    use ModuleTemplate;

    /**
     * @var array|null
     * @api Overwrite this property in your inheriting controller with your log config to restrict log readers
     */
    protected ?array $logConfiguration = null;

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
        if (null === $filter) {
            $filter = new Filter();
        }
        $reader = GeneralUtility::makeInstance(ConjunctionReader::class, $this->logConfiguration);
        $logs = $reader->findByFilter($filter);

        $this->view->assign('filter', $filter);
        $this->view->assign('logs', $logs);

        return $this->htmlResponse();
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
        $conjunctionReader = GeneralUtility::makeInstance(ConjunctionEraser::class, $this->logConfiguration);
        $conjunctionReader->delete($log);
        $this->redirect('filter');
    }

    /**
     * @throws StopActionException
     */
    public function deleteAlikeAction(string $component, int $level, string $message): void
    {
        $log = new Log('', 0.0, $component, $level, $message, []);
        $conjunctionReader = GeneralUtility::makeInstance(ConjunctionEraser::class, $this->logConfiguration);
        $conjunctionReader->deleteAlike($log);
        $this->redirect('filter');
    }

    /**
     * @return BackendUserAuthentication
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
