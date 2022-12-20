<?php

declare(strict_types=1);

namespace CoStack\LogsTests\Unit\Factory;

use CoStack\Logs\Factory\CollectionFactory;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CollectionFactoryTest extends UnitTestCase
{
    /**
     * @ticket https://gitlab.com/co-stack.com/co-stack.com/typo3-extensions/logs/-/issues/14
     */
    public function testFactoryIgnoresProcessorConfiguration(): void
    {
        $customConfig = [
            'MyVendor' => [
                'MyExtension' => [
                    'writerConfiguration' => [
                        'debug' => [
                            'TYPO3\CMS\Core\Log\Writer\DatabaseWriter' => [
                                'logTable' => 'tx_myvendir_myext_log',
                            ],
                        ],
                    ],
                    'processorConfiguration' => [
                        'debug' => [
                            'TYPO3\CMS\Core\Log\Processor\IntrospectionProcessor' => [
                                'appendFullBackTrace' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expcted = [
            [
                'class' => 'TYPO3\CMS\Core\Log\Writer\DatabaseWriter',
                'options' => ['logTable' => 'tx_myvendir_myext_log'],
            ],
        ];

        $collectionFactory = new CollectionFactory();
        $actual = $collectionFactory->getFlatWriterConfiguration($customConfig);

        $this->assertSame($expcted, $actual);
    }
}
