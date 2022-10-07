<?php

declare(strict_types=1);

namespace CoStack\Logs\Domain\Model;

use DateTime;
use DateTimeZone;
use TYPO3\CMS\Core\Log\LogLevel;

use function date_default_timezone_get;
use function sprintf;
use function strpos;

class Log
{
    public const FIELD_REQUEST_ID = 'request_id';
    public const FIELD_TIME_MICRO = 'time_micro';
    public const FIELD_COMPONENT = 'component';
    public const FIELD_LEVEL = 'level';
    public const FIELD_MESSAGE = 'message';
    public const FIELD_DATA = 'data';
    public string $requestId;
    public float $timeMicro;
    public string $component;
    public int $level;
    public string $message;
    public ?array $data;

    public function __construct(
        string $requestId,
        float $timeMicro,
        string $component,
        int $level,
        string $message,
        ?array $data
    ) {
        $this->requestId = $requestId;
        $this->timeMicro = $timeMicro;
        $this->component = $component;
        $this->level = $level;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * @noinspection PhpUnused Used in Partials/Log/List.html
     */
    public function getTimeMicroFormatted(): string
    {
        $timeMicro = (string)$this->timeMicro;
        if (false !== strpos($timeMicro, '.')) {
            $dateTime = DateTime::createFromFormat('U.u', $timeMicro);
        } elseif (false !== strpos($timeMicro, ' ')) {
            $dateTime = DateTime::createFromFormat('u U', $timeMicro);
        } else {
            $dateTime = DateTime::createFromFormat('U', $timeMicro);
        }
        $timezoneIdentifier = date_default_timezone_get();
        $dateTime->setTimezone(new DateTimeZone($timezoneIdentifier));

        return sprintf(
            '<span title="Timezone: %s">%s</span>',
            $timezoneIdentifier,
            $dateTime->format('Y-m-d H:i:s.u')
        );
    }

    /**
     * @noinspection PhpUnused Used in Partials/Log/List.html
     */
    public function getReadableLevel(): string
    {
        return LogLevel::getName($this->level);
    }
}
