<?php

declare(strict_types=1);

namespace CoStack\LogsDev\Controller;

use Faker\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

use function rand;
use function range;

class ModuleController extends ActionController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ModuleTemplateFactory $moduleTemplateFactory;

    public function __construct(ModuleTemplateFactory $moduleTemplateFactory)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    public function indexAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function createAction(int $count = 1): void
    {
        $generator = Factory::create('en');
        for ($i = 0; $i < $count; $i++) {
            if (rand(0, 1)) {
                $data = [];
                foreach (range(0, rand(0, 13)) as $j) {
                    $key = $generator->text(36);
                    switch (rand(0, 5)) {
                        case 0:
                            $value = null;
                            break;
                        case 1:
                            $value = [];
                            break;
                        case 2:
                            $value = $generator->randomNumber();
                            break;
                        case 3:
                            $value = $generator->text();
                            break;
                    }
                    $data[$key] = $value;
                }
                $this->logger->log(rand(0, 7), $generator->realTextBetween(16), $data);
            } else {
                $this->logger->log(rand(0, 7), $generator->realTextBetween(16));
            }
        }
        $this->redirect('index');
    }
}
