<?php

namespace Salmatron\Blimey;

use Salmatron\Blimey\Exception\BlimeyErrorException;
use Salmatron\Blimey\Exception\CompileErrorException;
use Salmatron\Blimey\Exception\CompileWarningException;
use Salmatron\Blimey\Exception\CoreErrorException;
use Salmatron\Blimey\Exception\CoreWarningException;
use Salmatron\Blimey\Exception\DeprecatedException;
use Salmatron\Blimey\Exception\IO\FileNotFoundException;
use Salmatron\Blimey\Exception\NoticeException;
use Salmatron\Blimey\Exception\ParseException;
use Salmatron\Blimey\Exception\RecoverableErrorException;
use Salmatron\Blimey\Exception\StrictException;
use Salmatron\Blimey\Exception\UserDeprecatedException;
use Salmatron\Blimey\Exception\UserErrorException;
use Salmatron\Blimey\Exception\UserNoticeException;
use Salmatron\Blimey\Exception\UserWarningException;
use Salmatron\Blimey\Exception\WarningException;

class Blimey
{
    // e.g.: "file_get_contents(/path/to/file): failed to open stream: No such file or directory"
    public const REGEX_FUNC_AND_ARGS_AND_MESSAGE = '/^(\w+)\((.*?)\): (.*)$/D';

    public static function registerErrorHandler()
    {
        set_error_handler([ static::class, 'errorHandler' ]);
    }

    public static function errorHandler(int $errno, string $errstr, string $errfile = null, int $errline = null, array $errcontext = null): bool
    {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            return false;
        }

        $arr = [];

        $matches = [];

        if (preg_match(static::REGEX_FUNC_AND_ARGS_AND_MESSAGE, $errstr, $matches) && $matches) {
            $funcName = $matches[1];
            $args = $matches[2];
            $message = $matches[3];

            if ($funcName && $message) {
                //if ($funcName === 'file_get_contents') {
                    if ($message === 'failed to open stream: No such file or directory') {
                        throw new FileNotFoundException('No such file or directory: ' . $args);
                    }
               // }
            }
        }

        switch ($errno) {
            case E_ERROR:
                throw new BlimeyErrorException($errstr, 0, $errno, $errfile, $errline);
            case E_WARNING:
                throw new WarningException($errstr, 0, $errno, $errfile, $errline);
            case E_PARSE:
                throw new ParseException($errstr, 0, $errno, $errfile, $errline);
            case E_NOTICE:
                throw new NoticeException($errstr, 0, $errno, $errfile, $errline);
            case E_CORE_ERROR:
                throw new CoreErrorException($errstr, 0, $errno, $errfile, $errline);
            case E_CORE_WARNING:
                throw new CoreWarningException($errstr, 0, $errno, $errfile, $errline);
            case E_COMPILE_ERROR:
                throw new CompileErrorException($errstr, 0, $errno, $errfile, $errline);
            case E_COMPILE_WARNING:
                throw new CompileWarningException($errstr, 0, $errno, $errfile, $errline);
            case E_USER_ERROR:
                throw new UserErrorException($errstr, 0, $errno, $errfile, $errline);
            case E_USER_WARNING:
                throw new UserWarningException($errstr, 0, $errno, $errfile, $errline);
            case E_USER_NOTICE:
                throw new UserNoticeException($errstr, 0, $errno, $errfile, $errline);
            case E_STRICT:
                throw new StrictException($errstr, 0, $errno, $errfile, $errline);
            case E_RECOVERABLE_ERROR:
                throw new RecoverableErrorException($errstr, 0, $errno, $errfile, $errline);
            case E_DEPRECATED:
                throw new DeprecatedException($errstr, 0, $errno, $errfile, $errline);
            case E_USER_DEPRECATED:
                throw new UserDeprecatedException($errstr, 0, $errno, $errfile, $errline);
        }


        return false;
    }
}
