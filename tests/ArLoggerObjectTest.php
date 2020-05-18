<?php

declare(strict_types=1);

namespace vsevolodryzhov\yii2ArLogger;

class ArLoggerObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateSuccess()
    {
        $obj = new ArLoggerObject(
            $model_name = 'SomeModelName',
            $model_id = 1,
            $attributes = ['key1' => 'value1', 'key2' => 'value2'],
            $action = 'actionUpdate',
            $description = 'Some description'
        );
        $this->assertAttributeEquals($model_name, 'model_name', $obj);
        $this->assertAttributeEquals($model_id, 'model_id', $obj);
        $this->assertAttributeEquals($attributes, 'attributes', $obj);
        $this->assertAttributeEquals($action, 'action', $obj);
        $this->assertAttributeEquals($description, 'description', $obj);
    }
}