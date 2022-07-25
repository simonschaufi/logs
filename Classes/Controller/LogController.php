<?php

declare(strict_types=1);

namespace CoStack\Logs\Controller;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use Psr\Http\Message\ResponseInterface;
use CoStack\Logs\Domain\Model\Filter;
use CoStack\Logs\Domain\Model\Log;
use CoStack\Logs\Log\Eraser\ConjunctionEraser;
use CoStack\Logs\Log\Reader\ConjunctionReader;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class LogController extends ActionController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ModuleTemplateFactory $moduleTemplateFactory;

    private ModuleTemplate $moduleTemplate;

    public function injectModuleTemplateFactory(ModuleTemplateFactory $moduleTemplateFactory): void
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
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
            if ($filter !== null) {
                $this->request->setArgument('filter', $filter);
                $this->arguments->getArgument('filter')->getPropertyMappingConfiguration()->allowAllProperties();
            }
        }
    }

    /**
     * @IgnoreValidation("filter")
     * @throws PropertyNotAccessibleException
     */
    public function filterAction(Filter $filter = null): ResponseInterface
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setTitle('Log');

        $this->addMainMenu('filter', 'Log');

        if ($filter === null) {
            $filter = new Filter();
        }
        $logs = (new ConjunctionReader())->findByFilter($filter);

        $this->view->assign('filter', $filter);
        $this->view->assign('logs', $logs);

        $this->moduleTemplate->setContent($this->view->render());

        return new HtmlResponse($this->moduleTemplate->renderContent());
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
        $numberOfDeletedRows = (new ConjunctionEraser())->delete($log);

        $this->addFlashMessage(sprintf(self::translate('actions.delete.message') ?? '', $numberOfDeletedRows));

        $this->redirect('filter');
    }

    /**
     * @throws StopActionException
     */
    public function deleteAlikeAction(string $component, int $level, string $message): void
    {
        $log = new Log('', 0.0, $component, $level, $message, []);
        $numberOfDeletedRows = (new ConjunctionEraser())->deleteAlike($log);

        $this->addFlashMessage(sprintf(self::translate('actions.delete.message') ?? '', $numberOfDeletedRows));
        $this->redirect('filter');
    }

    protected function addMainMenu(string $currentAction, string $currentController): void
    {
        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('LogsMainModuleMenu');
        $menu->addMenuItem(
            $menu->makeMenuItem()
                ->setTitle(self::translate('logs'))
                ->setHref($this->uriBuilder->uriFor('filter', null, 'Log'))
                ->setActive($currentAction === 'filter' && $currentController === 'Logs')
        );
        $menu->addMenuItem(
            $menu->makeMenuItem()
                ->setTitle(self::translate('deprecations'))
                ->setHref($this->uriBuilder->uriFor('filter', null, 'Deprecation'))
                ->setActive($currentAction === 'filter' && $currentController === 'Deprecation')
        );
        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected static function translate(string $key): ?string
    {
        return LocalizationUtility::translate($key, 'Logs');
    }
}
