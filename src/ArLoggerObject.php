<?php

declare(strict_types=1);

namespace vsevolodryzhov\yii2ArLogger;

class ArLoggerObject
{
    public $model_name;
    public $model_id;
    public $attributes;
    public $action;
    public $description;

    public function __construct(string $model_name, int $model_id, array $attributes, string $action, string $description = null)
    {
        $this->model_name = $model_name;
        $this->model_id = $model_id;
        $this->attributes = $attributes;
        $this->action = $action;
        $this->description = $description;
    }
}