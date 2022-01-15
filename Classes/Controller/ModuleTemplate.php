<?php

declare(strict_types=1);

namespace CoStack\Logs\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * @property Request $request
 * @property UriBuilder $uriBuilder
 * @property ViewInterface $view
 */
trait ModuleTemplate
{
    private ModuleTemplateFactory $moduleTemplateFactory;

    private LanguageService $languageService;

    private PageRenderer $pageRenderer;

    /**
     * @var array<string, non-empty-array<string, string>>
     */
    private array $modules = [
        'LLL:EXT:logs/Resources/Private/Language/locallang.xlf:logs' => [
            'action' => 'filter',
            'controller' => 'Log',
        ],
        'LLL:EXT:logs/Resources/Private/Language/locallang.xlf:deprecations' => [
            'action' => 'filter',
            'controller' => 'Deprecation',
        ],
    ];

    private array $requireJsModules = [
        'TYPO3/CMS/Logs/Module'
    ];

    public function injectModuleTemplateFactory(ModuleTemplateFactory $moduleTemplateFactory): void
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    public function injectLanguageService(LanguageServiceFactory $languageServiceFactory): void
    {
        $this->languageService = $languageServiceFactory->createFromUserPreferences($this->getBackendUser());
    }

    public function injectPageRenderer(PageRenderer $pageRenderer): void
    {
        $this->pageRenderer = $pageRenderer;
    }

    protected function htmlResponse(string $html = null): ResponseInterface
    {
        return parent::htmlResponse($html ?? $this->renderModuleTemplate());
    }

    protected function renderModuleTemplate(): string
    {
        /** @var ExtbaseRequestParameters $extbase */
        $extbase = $this->request->getAttribute('extbase');
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        foreach ($this->requireJsModules as $requireJsModule) {
            $this->pageRenderer->loadRequireJsModule($requireJsModule);
        }

        $menuRegistry = $moduleTemplate->getDocHeaderComponent()->getMenuRegistry();
        $menu = $menuRegistry->makeMenu();
        $menu->setIdentifier('module_selector');

        foreach ($this->modules as $label => $module) {
            $menuItem = $menu->makeMenuItem();
            $menuItem->setTitle($this->languageService->sL($label) ?: $label);
            $menuItem->setHref($this->uriBuilder->uriFor($module['action'], null, $module['controller']));
            $menuItem->setActive(
                $module['controller'] === $extbase->getControllerName()
                && $module['action'] === $extbase->getControllerActionName()
            );
            $menu->addMenuItem($menuItem);
        }

        $menuRegistry->addMenu($menu);
        $moduleTemplate->setContent($this->view->render());
        return $moduleTemplate->renderContent();
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
