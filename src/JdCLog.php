<?php declare(strict_types=1);


namespace Jdmm\JdCLog;

use Monolog\Formatter\LineFormatter;
use function sprintf;
use Swoft\Bean\Annotation\Mapping\Bean;
//use Swoft\Log\CLogger;
use Jdmm\JdCLog\CLogger;
use Swoft\Log\Handler\CEchoHandler;
use Swoft\Log\Handler\CFileHandler;

/**
 * Class JdCLog
 *
 * @since 2.0
 */
class JdCLog
{
    /**
     * @var CLogger
     */
    private static $cLogger;

    /**
     * Init console logger
     *
     * @param array $config
     */
    public static function init(array $config): void
    {
        if (self::$cLogger !== null) {
            return;
        }

        $name    = $config['name'] ?? '';
        $enable  = $config['enable'] ?? true;
        $output  = $config['output'] ?? true;
        $levels  = $config['levels'] ?? '';
        $logFile = $config['logFile'] ?? '';

        $lineFormatter = new LineFormatter();

        $cEchoHandler = new CEchoHandler();
        $cEchoHandler->setFormatter($lineFormatter);
        $cEchoHandler->setLevels($levels);
        $cEchoHandler->setOutput($output);

        $cFileHandler = new CFileHandler();
        $cFileHandler->setFormatter($lineFormatter);
        $cFileHandler->setLevels($levels);
        $cFileHandler->setLogFile($logFile);

        $cLogger = new CLogger();
        $cLogger->setName($name);
        $cLogger->setEnable($enable);
        $cLogger->setHandlers([$cEchoHandler, $cFileHandler]);

        self::$cLogger = $cLogger;
    }

    /**
     * Debug message
     *
     * @param string $message
     * @param array  $params
     */
    public static function debug($message, ...$params): void
    {
        self::_log('debug', $message, ...$params);
    }

    /**
     * Info message
     *
     * @param string $message
     * @param array  $params
     */
    public static function info($message, ...$params): void
    {
        self::_log('info', $message, ...$params);
    }

    /**
     * Warning message
     *
     * @param string $message
     * @param array  $params
     */
    public static function warning($message, ...$params): void
    {
        self::_log('warning', $message, ...$params);
    }

    /**
     * Error message
     *
     * @param string $message
     * @param array  $params
     */
    public static function error($message, ...$params): void
    {
        self::_log('error', $message, ...$params);
    }

    private static function _log($method, $message, ...$params) {
        if(self::$cLogger === null) {
            $config = \Swoft::$app->getCLoggerConfig();
            self::init($config);
        }
        $data = [];
        if($params) {
            $data[] = $message;
            $data = array_merge($data, $params);
        } else {
            $data = $message;
        }

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        if(defined("STDOUT")) {
            if (!function_exists('posix_isatty') or !@posix_isatty(STDOUT)) {
                $data = htmlentities($data);
            }
        }

        self::$cLogger->$method($data, []);
    }
}
