<?php
namespace Test\Sdfcloud;

use \UA1Labs\Fire\Test\TestCase;
use \Sdfcloud\Di;
use \Sdfcloud\DiException;
use \Sdfcloud\Di\NotFoundException;

class DiTestSuite extends TestCase {
    /**
     * The Di object instance
     * @var \Sdfcloud\Di
     */
    private $di;

    /**
     * beforeEach callback.
     */
    public function beforeEach() {
        $this->di = new Di();
    }

    /**
     * afterEach callback.
     */
    public function afterEach() {
        unset($this->di);
    }

    public function testConstructor() {
        $this->should('The constructor should not throw an execption and be an instance of Di.');
        $this->assert($this->di instanceof Di);
    }

    public function testSetObject() {
        $this->should('Put an object in the cache without an exception.');
        try {
            $this->di->set('TestObject', new TestClassC());
            $this->assert(true);
        } catch (DiException $e) {
            $this->assert(false);
        }

        $this->should('Put an array in the cache.');
        $this->di->clearObjectCache();
        $this->di->set('TestObject', []);
        $this->assert($this->di->get('TestObject') === []);

        $this->should('Put a string in the cache.');
        $this->di->clearObjectCache();
        $this->di->set('TestObject', 'Test');
        $this->assert($this->di->get('TestObject') === 'Test');
    }

    public function testSetCallable() {
        $callable = function() {
            return new TestClassC;
        };

        $this->should('Allow me to set a callable object in the object cache.');
        $this->di->set('Test\Callable\Function', $callable);
        $objectCache = $this->di->getObjectCache();
        $this->assert(
            isset($objectCache['Test\Callable\Function'])
            && $objectCache['Test\Callable\Function'] === $callable
        );

        $this->should('Allow me to call the object from cache and the callable function should be executed.');
        $this->assert($this->di->get('Test\Callable\Function') instanceof TestClassC);
        $this->di->clearObjectCache();

        $this->should('Allow me to override a class definition with a callable.');
        $testClassC = $this->di->get('Test\Sdfcloud\TestClassC');
        $this->di->set('Test\Sdfcloud\TestClassC', $callable);
        $objectCache = $this->di->getObjectCache();
        $this->assert(
            isset($objectCache['Test\Sdfcloud\TestClassC'])
            && $objectCache['Test\Sdfcloud\TestClassC'] === $callable
        );

        $this->should('Resolve "TestClassD" properly with the callable overridden.');
        $testClassD = $this->di->get('Test\Sdfcloud\TestClassD');
        $this->assert($testClassD instanceof TestClassD);
    }

    public function testHasObject() {
        $this->should('Return true when a class can be resolved.');
        $result = $this->di->has('\Test\Sdfcloud\TestClassC');
        $this->assert($result === true);
        $this->di->clearObjectCache();

        $this->should('Return false when a class has a dependency that cannot be resolved.');
        $result = $this->di->has('\Test\Sdfcloud\TestClassCC');
        $this->assert($result === false);
        $this->di->clearObjectCache();

        $this->should('Return false when a class has a circular dependency issue.');
        $result = $this->di->has('\Test\Sdfcloud\TestClassAA');
        $this->assert($result === false);
        $this->di->clearObjectCache();

        $this->should('Return false when a class does not exist.');
        $result = $this->di->has('Undefined');
        $this->assert($result === false);
        $this->di->clearObjectCache();
    }

    public function testGetObject() {
        $this->should('Resolve all dependencies for TestClassA and return the TestClassA object.');
        $testClassA = $this->di->get('Test\Sdfcloud\TestClassA');
        $this->assert($testClassA instanceof TestClassA);

        $this->should('Have placed TestClassA, TestClassB, and TestClassC within the object cache.');
        $objectCache = $this->di->getObjectCache();
        $this->assert(
            isset($objectCache['Test\Sdfcloud\TestClassA'])
            && isset($objectCache['Test\Sdfcloud\TestClassB'])
            && isset($objectCache['Test\Sdfcloud\TestClassC'])
            && $objectCache['Test\Sdfcloud\TestClassA'] instanceof TestClassA
            && $objectCache['Test\Sdfcloud\TestClassB'] instanceof TestClassB
            && $objectCache['Test\Sdfcloud\TestClassC'] instanceof TestClassC
        );

        $this->di->clearObjectCache();

        $this->should('Resolve all dependencies for TestClassD and return it.');
        $testClassD = $this->di->get('Test\Sdfcloud\TestClassD');
        $this->assert($testClassD instanceof TestClassD);

        $this->should('Have set ::A as TestClassA on TestClassD object.');
        $this->assert(isset($testClassD->A) && $testClassD->A instanceof TestClassA);

        $this->should('Have set ::B as TestClassB on TestClassD object.');
        $this->assert(isset($testClassD->B) && $testClassD->B instanceof TestClassB);

        $this->should('Have set ::C as TestClassC on TestClassB.');
        $this->assert(isset($testClassD->B->C) && $testClassD->B->C instanceof TestClassC);

        $this->should('Throw a NotFoundException if a the class you are trying to get does not exists.');
        try {
            $this->di->get('UndefinedClass');
            $this->assert(false);
        } catch (NotFoundException $e) {
            $this->assert(true);
        }

        $this->should('Throw a NotFoundException if a circular dependency is detected.');
        try {
            $this->di->get('Test\Sdfcloud\TestClassAA');
            $this->assert(false);
        } catch (NotFoundException $e) {
            $this->assert(true);
        }
    }

    public function testGetWithObject() {
        $this->should('Return a TestClassB object.');
        $testClassC = $this->di->get('Test\Sdfcloud\TestClassC');
        $dependencies = [$testClassC];
        $testClassB = $this->di->getWith('Test\Sdfcloud\TestClassB', $dependencies);
        $this->assert($testClassB instanceof TestClassB);

        $this->should('Prove that TestClassB has a $C variable that is TestClassC.');
        $this->assert($testClassB->C instanceof TestClassC);

        $this->should('Prove that the object cache does not contain a TestClassB');
        $objectCache = $this->di->getObjectCache();
        $this->assert(!isset($objectCache['TestClassB']));

        $this->should('Throw and exception if a the class you are trying to get does not exists.');
        try {
            $this->di->getWith('UndefinedClass', []);
            $this->assert(false);
        } catch (DiException $e) {
            $this->assert(true);
        }
    }

    public function testGetObjectCache() {
        $this->di->set('TestObject', new TestClassC());
        $objectCache = $this->di->getObjectCache();

        $this->should('Return an object cache array with a key "TestObject".');
        $this->assert(isset($objectCache['TestObject']));

        $this->should('Return an object cache with the object we put into it.');
        $this->assert($objectCache['TestObject'] instanceof TestClassC);
    }

    public function testClearObjectCache() {
        $this->should('Remove all objects from the object cache');
        $this->di->set('TestObject', new TestClassC());
        $this->di->clearObjectCache();
        $objectCache = $this->di->getObjectCache();
        $this->assert(empty($objectCache));
    }
}

/**
 * Test classes for testing dependency injection
 */
class TestClassA
{
    public function __construct(TestClassB $B)
    {}
}

class TestClassB
{
    public $C;

    public function __construct(TestClassC $C)
    {
        $this->C = $C;
    }
}

class TestClassC
{}

class TestClassD
{
    public $A;
    public $B;

    public function __construct(TestClassA $A, TestClassB $B)
    {
        $this->A = $A;
        $this->B = $B;
    }
}

class TestClassAA
{
    public function __construct(TestClassBB $BB)
    {}
}

class TestClassBB
{
    public function __construct(TestClassAA $AA)
    {}
}

