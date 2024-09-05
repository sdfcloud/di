<?php
namespace Test\Sdfcloud\Di;

use \UA1Labs\Fire\Test\TestCase;
use \Sdfcloud\Di\ClassDefinition;
use \ReflectionClass;

class ClassDefinitionTestSuite extends TestCase {
    /**
     * The Di ClassDefinition Class
     * @var \Sdfcloud\Di\ClassDefinition
     */
    private $classDefinition;

    public function beforeEach() {
        $this->classDefinition = new ClassDefinition('Test\Sdfcloud\Di\MyTestClass');
    }

    public function afterEach() {
        unset($this->classDefinition);
    }

    public function testConstructor() {
        $this->should('Not throw an exception when the class is constructed');
        $this->assert(true);

        $this->should('Have set a serviceId of "Test\Sdfcloud\Di\MyTestClass".');
        $this->assert($this->classDefinition->serviceId === 'Test\Sdfcloud\Di\MyTestClass');

        $this->should('Have set a classDef as a ReflectionClass object.');
        $this->assert($this->classDefinition->classDef instanceof ReflectionClass);

        $this->should('Have set a dependency of "Test\Sdfcloud\Di\MyDependentClass"');
        $this->assert(
            isset($this->classDefinition->dependencies[0])
            && $this->classDefinition->dependencies[0] === 'Test\Sdfcloud\Di\MyDependentClass'
        );
    }
}

/**
 * Test classes need for tests.
 */


class MyTestClass {
    public function __construct(MyDependentClass $stdClass) {}
}

class MyDependentClass {}
