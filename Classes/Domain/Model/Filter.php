<?php

declare(strict_types=1);

namespace CoStack\Logs\Domain\Model;

use Psr\Log\LogLevel;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class Filter
{
    public const SORTING_DESC = 'DESC';
    public const SORTING_ASC = 'ASC';

    protected string $requestId = '';

    protected string $level = LogLevel::NOTICE;

    protected ?int $fromTime = null;

    protected ?int $toTime = null;

    protected bool $showData = false;

    protected string $component = '';

    protected bool $fullMessage = true;

    protected int $limit = 150;

    protected string $orderField = Log::FIELD_TIME_MICRO;

    protected string $orderDirection = self::SORTING_DESC;

    public function getRequestId(): string
    {
        return $this->requestId;
    }

    public function setRequestId(string $requestId): void
    {
        $this->requestId = $requestId;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    public function getFromTime(): ?int
    {
        return $this->fromTime;
    }

    public function setFromTime(int $fromTime = null): void
    {
        $this->fromTime = $fromTime;
    }

    public function getToTime(): ?int
    {
        return $this->toTime;
    }

    public function setToTime(int $toTime = null): void
    {
        $this->toTime = $toTime;
    }

    public function isShowData(): bool
    {
        return $this->showData;
    }

    public function setShowData(bool $showData): void
    {
        $this->showData = $showData;
    }

    public function getComponent(): string
    {
        return $this->component;
    }

    public function setComponent(string $component): void
    {
        $this->component = $component;
    }

    public function isFullMessage(): bool
    {
        return $this->fullMessage;
    }

    public function setFullMessage(bool $fullMessage): void
    {
        $this->fullMessage = $fullMessage;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getOrderField(): string
    {
        return $this->orderField;
    }

    public function setOrderField(string $orderField): void
    {
        if (array_key_exists($orderField, $this->getOrderFields())) {
            $this->orderField = $orderField;
        }
    }

    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    public function setOrderDirection(string $orderDirection): void
    {
        $this->orderDirection = $orderDirection;
    }

    public function getLogLevels(): array
    {
        return [
            LogLevel::EMERGENCY => LogLevel::EMERGENCY . ' (' . LogLevel::EMERGENCY . ')',
            LogLevel::ALERT => LogLevel::ALERT . ' (' . LogLevel::ALERT . ')',
            LogLevel::CRITICAL => LogLevel::CRITICAL . ' (' . LogLevel::CRITICAL . ')',
            LogLevel::ERROR => LogLevel::ERROR . ' (' . LogLevel::ERROR . ')',
            LogLevel::WARNING => LogLevel::WARNING . ' (' . LogLevel::WARNING . ')',
            LogLevel::NOTICE => LogLevel::NOTICE . ' (' . LogLevel::NOTICE . ')',
            LogLevel::INFO => LogLevel::INFO . ' (' . LogLevel::INFO . ')',
            LogLevel::DEBUG => LogLevel::DEBUG . ' (' . LogLevel::DEBUG . ')',
        ];
    }

    public function getOrderFields(): array
    {
        return [
            Log::FIELD_TIME_MICRO => LocalizationUtility::translate('filter.time_micro', 'logs'),
            Log::FIELD_REQUEST_ID => LocalizationUtility::translate('filter.request_id', 'logs'),
            Log::FIELD_COMPONENT => LocalizationUtility::translate('filter.component', 'logs'),
            Log::FIELD_LEVEL => LocalizationUtility::translate('filter.level', 'logs'),
        ];
    }

    public function getOrderDirections(): array
    {
        return [
            static::SORTING_DESC => LocalizationUtility::translate('filter.desc', 'logs'),
            static::SORTING_ASC => LocalizationUtility::translate('filter.asc', 'logs'),
        ];
    }
}
