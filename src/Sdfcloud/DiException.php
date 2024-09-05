<?php

namespace Sdfcloud;

use \Exception;
use \Psr\Container\ContainerExceptionInterface;

/**
 * Exception thrown from di library.
 * @package Sdfcloud
 */
class DiException extends Exception implements ContainerExceptionInterface {
    const ERROR_CLASS_NOT_FOUND = 'Class "%s" does not exist and it definition cannot be registered with sdfcloud-DI.';
    const ERROR_CIRCULAR_DEPENDENCY = 'While trying to resolve class "%s", sdfcloud-di found that there was a cirular dependency caused by the class "%s".';
}
