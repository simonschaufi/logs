<?php

declare(strict_types=1);

namespace CoStack\LogsDev\Controller;

use Faker\Factory;
use Faker\Generator;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LogLevel;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

use function rand;
use function sprintf;

class ModuleController extends ActionController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const ENABLE_DEPRECATION_LOGGER_MESSAGE = 'Deprecation logging is disabled in your TYPO3. You must set TYPO3_CONTEXT=Development or LOG/TYPO3/CMS/deprecations/writerConfiguration/notice/disabled=false to be able to create dummy deprecations.';
    private const LOG_LEVELS = [
        LogLevel::DEBUG,
        LogLevel::INFO,
        LogLevel::NOTICE,
        LogLevel::WARNING,
        LogLevel::ERROR,
        LogLevel::CRITICAL,
        LogLevel::ALERT,
        LogLevel::EMERGENCY,
    ];
    public function __construct(private ModuleTemplateFactory $moduleTemplateFactory)
    {
    }

    /**
     * @noinspection PhpUnused Plugin action called by Extbase
     */
    public function indexAction(): ResponseInterface
    {
        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger('TYPO3.CMS.deprecations');
        $deprecationLoggingEnabled = !empty($logger->getWriters());
        if (!$deprecationLoggingEnabled) {
            $this->addFlashMessage(self::ENABLE_DEPRECATION_LOGGER_MESSAGE, '', AbstractMessage::INFO);
        }
        $this->view->assign('deprecationLoggingEnabled', $deprecationLoggingEnabled);

        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    /**
     * @noinspection PhpUnused Plugin action called by Extbase
     */
    public function createLogsAction(int $count = 1): RedirectResponse
    {
        $generator = Factory::create('en');
        for ($i = 0; $i < $count; $i++) {
            $data = $this->generateRandomData($generator);
            $this->logger->log(rand(0, 7), $generator->realTextBetween(16), $data);
        }
        $this->addFlashMessage(sprintf('Created %d dummy log entries', $count));
        $uri = $this->uriBuilder->uriFor('index');
        return new RedirectResponse($uri);
    }

    /**
     * @noinspection PhpUnused Plugin action called by Extbase
     */
    public function createDeprecationsAction(int $count = 1): RedirectResponse
    {
        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger('TYPO3.CMS.deprecations');
        if (empty($logger->getWriters())) {
            $this->addFlashMessage(self::ENABLE_DEPRECATION_LOGGER_MESSAGE, '', AbstractMessage::ERROR);
            $uri = $this->uriBuilder->uriFor('index');
            return new RedirectResponse($uri);
        }
        $generator = Factory::create('en');
        for ($i = 0; $i < $count; $i++) {
            $data = $this->generateRandomData($generator);
            $logger->notice($generator->realTextBetween(16), $data);
        }
        $this->addFlashMessage(sprintf('Created %d dummy deprecations', $count));
        $uri = $this->uriBuilder->uriFor('index');
        return new RedirectResponse($uri);
    }

    public function generateRandomData(Generator $generator): array
    {
        $data = [];

        // 33% empty data
        if (rand(0, 2) === 0) {
            return $data;
        }

        // Between 1 and 13 entries
        for ($i = rand(1, 13); $i > 0; $i--) {
            // Random data key
            $key = $generator->text(36);
            // Random data type
            $value = match (rand(0, 3)) {
                0 => [],
                1 => $generator->randomNumber(),
                2 => $generator->text(),
                default => null,
            };
            $data[$key] = $value;
        }

        return $data;
    }
}
