<?php

declare(strict_types=1);

namespace vsevolodryzhov\yii2ArLogger;

class DbLoggerStorage implements ArLoggerStorageInterface
{

    public function store(ArLoggerObject $object): bool
    {
        $model = new LogModel();
        $model->model_name = $object->model_name;
        $model->model_id = $object->model_id;
        $model->attributes = $object->attributes;
        $model->action = $object->action;
        $model->description = $object->description;
        return $model->save();
    }
}