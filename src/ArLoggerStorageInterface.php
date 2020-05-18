<?php

declare(strict_types=1);

namespace vsevolodryzhov\yii2ArLogger;

interface ArLoggerStorageInterface
{
    /**
     * Store log data
     * @param ArLoggerObject $object
     * @return bool
     */
    public function store(ArLoggerObject $object): bool;
}