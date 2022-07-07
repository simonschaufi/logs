<?php

declare(strict_types=1);

namespace CoStack\Logs\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Log\Writer\FileWriter;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class DeprecationController extends ActionController
{
    private const STATIC_PREFIX = ' component="TYPO3.CMS.deprecations":';

    private ModuleTemplateFactory $moduleTemplateFactory;

    private ModuleTemplate $moduleTemplate;

    public function injectModuleTemplateFactory(ModuleTemplateFactory $moduleTemplateFactory): void
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    public function filterAction(): ResponseInterface
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setTitle('Deprecation');

        $this->addMainMenu('filter', 'Deprecation');

        $file = $this->getLogFilePath();
        $stream = fopen($file, 'rb');

        // Counts and messages are collected separately for easier sorting.
        $deprecations = [];
        $count = [];
        $current = fgets($stream);
        while ($peek = fgets($stream)) {
            $line = $current;
            $pos = strpos($line, self::STATIC_PREFIX);
            if (false !== $pos) {
                $line = substr($line, $pos + 36);
            }
            while (true) {
                if ($peek === false) {
                    break;
                }
                $peekPos = strpos($peek, self::STATIC_PREFIX);
                if (false !== $peekPos) {
                    break;
                }
                $line .= $peek;
                $peek = fgets($stream);
            }
            $hash = sha1($line);
            $deprecations[$hash] = $line;
            $count[$hash]++;
            $current = $peek;
        }
        fclose($stream);

        asort($deprecations);

        // Add count to all messages
        foreach ($deprecations as $hash => $message) {
            $deprecations[$hash] = [
                'message' => $message,
                'count' => $count[$hash],
            ];
        }

        $this->view->assign('deprecations', $deprecations);

        $this->moduleTemplate->setContent($this->view->render());

        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * 1. Create a new log file
     * 2. Copy all lines from the old to the new file which do not match the hash
     * 3. Replace the old file with the new file
     * 4. Profit
     */
    public function deleteAction(string $hash): void
    {
        $file = $this->getLogFilePath();

        $newFile = $file . uniqid('', true);
        $newStream = fopen($newFile, 'xb');
        if (false === $newStream) {
            $this->addFlashMessage(
                LocalizationUtility::translate('error.log_file_not_writable', 'logs'),
                LocalizationUtility::translate('error', 'logs'),
                AbstractMessage::ERROR
            );
            $this->redirect('filter');
        }
        $oldStream = fopen($file, 'rb');

        $count = 0;
        $current = fgets($oldStream);
        while ($peek = fgets($oldStream)) {
            $content = $current;
            $probe = $current;
            $pos = strpos($probe, self::STATIC_PREFIX);
            if (false !== $pos) {
                $probe = substr($probe, $pos + 36);
            }
            while (true) {
                if ($peek === false) {
                    break;
                }
                $peekPos = strpos($peek, self::STATIC_PREFIX);
                if (false !== $peekPos) {
                    break;
                }
                $probe .= $peek;
                $content .= $peek;
                $peek = fgets($oldStream);
            }
            $current = $peek;
            if (sha1($probe) === $hash) {
                $count++;
                continue;
            }
            fwrite($newStream, $content);
        }

        fclose($newStream);
        fclose($oldStream);

        unlink($file);
        rename($newFile, $file);

        $this->addFlashMessage(LocalizationUtility::translate('deleted_count', 'logs', [$count]));

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

    protected function getLogFilePath(): string
    {
        return (new FileWriter(['logFileInfix' => 'deprecations']))->getLogFile();
    }

    protected static function translate($key): ?string
    {
        return LocalizationUtility::translate($key, 'Logs');
    }
}
