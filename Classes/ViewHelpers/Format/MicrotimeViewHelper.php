<?php

declare(strict_types=1);

namespace CoStack\Logs\ViewHelpers\Format;

use Closure;
use DateTime;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class MicrotimeViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('microTime', 'float', 'Value returned by microtime(true)', true);
        $this->registerArgument('format', 'string', 'Resulting format', false, 'Y-m-d H:i:s.u');
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $microTime = (string)$arguments['microTime'];
        $format = $arguments['format'];

        if (strpos($microTime, '.') !== false) {
            $dateTime = DateTime::createFromFormat('U.u', $microTime);
        } elseif (strpos($microTime, ' ') !== false) {
            $dateTime = DateTime::createFromFormat('u U', $microTime);
        } else {
            $dateTime = DateTime::createFromFormat('U', $microTime);
        }

        return $dateTime->format($format);
    }
}
