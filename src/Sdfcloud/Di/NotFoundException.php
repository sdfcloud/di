<?php
namespace Sdfcloud\Di;

use \Exception;
use \Psr\Container\NotFoundExceptionInterface;

/**
 * Exception thrown from the Di library.
 */
class NotFoundException extends Exception implements NotFoundExceptionInterface {
    const ERROR_NOT_FOUND_IN_CONTAINER = '"%s" could not be resolved by Di.';
}
