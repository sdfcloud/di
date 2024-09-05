<?php
namespace Sdfcloud\Di;

use \ReflectionClass;
use \Sdfcloud\DiException;
use \ReflectionException;

/**
 * This class is meant to wrap a class being registered with Di.
 * @package Sdfcloud
 */
class ClassDefinition {

    /**
     * The ID of the service
     * @var string
     */
    public $serviceId;

    /**
     * A reflection object that defines this particular service.
     * @var \ReflectionClass
     */
    public $classDef;

    /**
     * An array that stores the names of the variables defined in the $closure.
     * The names of the variables of the $closure defines what services this
     * service depends on.
     * @var string[]
     */
    public $dependencies;

    /**
     * The constructor sets up the service object to be stored until zulfberht
     * determines that the service should be relocated into the ulfberht
     * runtime environment.
     * @param string $classname The class you would like to wrap in an ulfberhtservice.
     */
    public function __construct($classname) {
        $this->serviceId = $classname;
        $this->classDef = new ReflectionClass($classname);
        $this->dependencies = [];

        //use reflection to get dependencies then store them
        $constructor = $this->classDef->getConstructor();
        if ($constructor) {
            $parameters = $constructor->getParameters();
            if (!empty($parameters)) {
                foreach ($parameters as $parameter) {
                    try {
                        $dependency = $parameter->getType();
                        if ($dependency) {
                            $this->dependencies[] = $dependency->getName();
                        } else {
                            $this->dependencies[] = '';
                        }
                    } catch (ReflectionException $e) {
                        throw new DiException($e->getMessage());
                    }
                }
            }
        }
    }
}
