<?php declare(strict_types=1);

namespace Jdmm\JdCLog;

use function count;
use function debug_backtrace;
use function sprintf;

/**
 * Console logger
 *
 * @since 2.0
 */
class CLogger extends \Swoft\Log\CLogger
{
    /**
     * Add debug trace
     *
     * @param string $message
     *
     * @return string
     */
    public function getTrace(string $message): string
    {
        $stackStr = '';
        $traces   = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        $count    = count($traces);

        if ($count >= 7) {
            $info = $traces[5];
            if (isset($info['file'], $info['class'])) {
                $class    = $info['class'];
                $lineNum  = $traces[4]['line'];
                $function = $info['function'];
                $stackStr = sprintf('%s:%s(%s)', $class, $function, $lineNum);
            }
        }

        if (!empty($stackStr)) {
            $message = sprintf('%s %s', $stackStr, $message);
        }

        return $message;
    }
}
