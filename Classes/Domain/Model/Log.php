<?php

declare(strict_types=1);

namespace CoStack\Logs\Domain\Model;

use TYPO3\CMS\Core\Log\LogLevel;

class Log
{
    public const FIELD_REQUEST_ID = 'request_id';
    public const FIELD_TIME_MICRO = 'time_micro';
    public const FIELD_COMPONENT = 'component';
    public const FIELD_LEVEL = 'level';
    public const FIELD_MESSAGE = 'message';
    public const FIELD_DATA = 'data';

    protected string $requestId = '';

    protected float $timeMicro = 0.0;

    protected string $component = '';

    protected int $level = 0;

    protected string $message = '';

    protected array $data = [];

    public function __construct(
        string $requestId,
        float $timeMicro,
        string $component,
        int $level,
        string $message,
        array $data
    ) {
        $this->requestId = $requestId;
        $this->timeMicro = $timeMicro;
        $this->component = $component;
        $this->level = $level;
        $this->message = $message;
        $this->data = $data;
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }

    public function setRequestId(string $requestId): void
    {
        $this->requestId = $requestId;
    }

    public function getTimeMicro(): float
    {
        return $this->timeMicro;
    }

    public function setTimeMicro(float $timeMicro): void
    {
        $this->timeMicro = $timeMicro;
    }

    public function getComponent(): string
    {
        return $this->component;
    }

    public function setComponent(string $component): void
    {
        $this->component = $component;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getReadableLevel(): string
    {
        return LogLevel::getName($this->level);
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
