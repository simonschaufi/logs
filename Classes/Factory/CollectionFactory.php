<?php

declare(strict_types=1);

namespace CoStack\Logs\Factory;

use CoStack\Logs\Log\Eraser\DatabaseEraser;
use CoStack\Logs\Log\Eraser\Eraser;
use CoStack\Logs\Log\Eraser\EraserCollection;
use CoStack\Logs\Log\Reader\DatabaseReader;
use CoStack\Logs\Log\Reader\Reader;
use CoStack\Logs\Log\Reader\ReaderCollection;
use JsonException;
use TYPO3\CMS\Core\Log\Writer\DatabaseWriter;
use TYPO3\CMS\Core\Log\Writer\WriterInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function array_values;
use function is_array;
use function json_encode;
use function sha1;

use const JSON_THROW_ON_ERROR;

/**
 * @noinspection PhpUnused Used as factory for the injection of EraserCollection and ReaderCollection
 */

class CollectionFactory
{
    protected const WRITER_ERASER_MAPPING = [
        DatabaseWriter::class => DatabaseEraser::class,
    ];
    protected const WRITER_READER_MAPPING = [
        DatabaseWriter::class => DatabaseReader::class,
    ];

    /**
     * @noinspection PhpUnused Used as factory for the injection of EraserCollection
     * @throws JsonException
     */
    public function createEraserCollection(array $customConfiguration = null): EraserCollection
    {
        $erasers = $this->createObjects(self::WRITER_ERASER_MAPPING, $customConfiguration);
        return new EraserCollection(array_values($erasers));
    }

    /**
     * @noinspection PhpUnused Used as factory for the injection of ReaderCollection
     * @throws JsonException
     */
    public function createReaderCollection(array $customConfiguration = null): ReaderCollection
    {
        $readers = $this->createObjects(self::WRITER_READER_MAPPING, $customConfiguration);
        return new ReaderCollection(array_values($readers));
    }

    /**
     * @return array<DatabaseEraser|DatabaseReader>
     * @throws JsonException
     */
    protected function createObjects(array $mapping, ?array $customConfiguration): array
    {
        $objects = [];

        foreach ($this->getFlatWriterConfiguration($customConfiguration) as $writer) {
            $class = $mapping[$writer['class']] ?? null;
            if (null !== $class) {
                $key = $this->getUniqueConfigKey($class, $writer['options']);
                if (!isset($objects[$key])) {
                    $logWriter = GeneralUtility::makeInstance($writer['class'], $writer['options']);
                    $objects[$key] = new $class($logWriter);
                }
            }
        }

        return array_values($objects);
    }

    /**
     * @param class-string<Eraser|Reader> $eraserClass
     * @throws JsonException
     */
    protected function getUniqueConfigKey(string $eraserClass, array $writerConfig): string
    {
        $configValues = [];
        foreach ($eraserClass::getDefaultConfigForUniqueKeys() as $field => $value) {
            $configValues[] = $writerConfig[$field] ?? $value;
        }
        return sha1(json_encode($configValues, JSON_THROW_ON_ERROR));
    }

    /**
     * @return array<array{class: class-string<WriterInterface>, options: array}>
     */
    public function getFlatWriterConfiguration(?array $customWriterConfig = null): array
    {
        $writerConfig = $customWriterConfig ?? $GLOBALS['TYPO3_CONF_VARS']['LOG'] ?? [];

        return $this->getWriters($writerConfig);
    }

    /**
     * @return array<array{class: class-string<WriterInterface>, options: array}>
     */
    protected function getWriters(array $configuration, array &$writers = []): array
    {
        foreach ($configuration as $key => $value) {
            if ('writerConfiguration' !== $key) {
                $this->getWriters($value, $writers);
            } elseif (is_array($value)) {
                $this->getWritersForLevel($value, $writers);
            }
        }

        return $writers;
    }

    /**
     * @param array<array{class: class-string<WriterInterface>, options: array}> $value
     */
    public function getWritersForLevel(array $value, array &$writers): void
    {
        foreach ($value as $writersForLevel) {
            if (is_array($writersForLevel)) {
                foreach ($writersForLevel as $writerClass => $writerOptions) {
                    if (
                        is_array($writerOptions)
                        && false === ($writerOptions['disabled'] ?? false)
                    ) {
                        unset($writerOptions['disabled']);
                        $writers[] = [
                            'class' => $writerClass,
                            'options' => is_array($writerOptions) ? $writerOptions : [],
                        ];
                    }
                }
            }
        }
    }
}
