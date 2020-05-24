<?php

declare(strict_types=1);


namespace vsevolodryzhov\yii2ArLogger;


use PHPUnit_Framework_TestCase;
use ReflectionMethod;

class ArLoggerBehaviorTest extends PHPUnit_Framework_TestCase
{
    public function testFloatProcess()
    {
        $method = new ReflectionMethod(ArLoggerBehavior::class, 'processFloatValue');
        $method->setAccessible(true);
        $obj = new ArLoggerBehavior();
        $this->assertSame('10.1', $method->invokeArgs($obj, [10.1]));
    }

    public function testUnsetAttributes()
    {
        $method = new ReflectionMethod(ArLoggerBehavior::class, 'unsetExcludedAttributes');
        $method->setAccessible(true);

        $obj = new ArLoggerBehavior();
        $obj->excludedAttributes = ['attr1', 'attr3'];
        $this->assertSame(['attr2' => 2, 'attr4' => 4], $method->invokeArgs($obj, [['attr1' => 1, 'attr2' => 2, 'attr3' => 3, 'attr4' => 4]]));
    }
}