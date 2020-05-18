<?php

declare(strict_types=1);

namespace vsevolodryzhov\yii2ArLogger;

use yii\base\Behavior;
use yii\base\Component;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\di\NotInstantiableException;
use yii\helpers\StringHelper;

class ArLoggerBehavior extends Behavior
{
    const ACTION_CHANGE = 'CHANGE';
    const ACTION_INSERT = 'INSERT';
    const ACTION_UPDATE = 'UPDATE';
    const ACTION_DELETE = 'DELETE';

    public $storage;
    public $excludedAttributes;

    public function __construct($config = [])
    {
        parent::__construct($config);
        if (empty($this->storage)) {
            $this->storage = new DbLoggerStorage();
        }  elseif (!in_array(ArLoggerStorageInterface::class, class_implements($this->storage))) {
            throw new NotInstantiableException(sprintf('%s must implement %s interface', $this->storage, ArLoggerStorageInterface::class));
        } else {
            $this->storage = new $this->storage;
        }
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'addLog',
            ActiveRecord::EVENT_AFTER_UPDATE => 'addLog',
            ActiveRecord::EVENT_AFTER_DELETE => 'addDeleteLog'
        ];
    }

    /**
     * Remove excluded attributes from input array
     * @param array $excludedAttributes
     * @return array Return attributes array for logging
     */
    private function unsetExcludedAttributes(array $excludedAttributes): array
    {
        if (!empty($this->excludedAttributes)) {
            foreach ($this->excludedAttributes as $attribute) {
                unset($excludedAttributes[$attribute]);
            }
        }

        return $excludedAttributes;
    }

    /**
     * @param $event_name
     * @return string
     */
    private function setAction($event_name): string
    {
        switch ($event_name) {
            case ActiveRecord::EVENT_AFTER_INSERT:
                return self::ACTION_INSERT;
                break;
            case ActiveRecord::EVENT_AFTER_UPDATE:
                return self::ACTION_UPDATE;
                break;
            case ActiveRecord::EVENT_AFTER_DELETE:
                return self::ACTION_DELETE;
                break;
            default:
                return self::ACTION_CHANGE;
                break;
        }
    }

    /**
     * Convert float value to string
     * @param $value
     * @return string
     */
    private function processFloatValue($value): string
    {
        return is_float($value) ? StringHelper::floatToString($value) : $value;
    }

    private function extractChangedAttributeValues(Component $owner, array $changedAttributes): array
    {
        $changedInfo = [];

        foreach ($changedAttributes as $attributeName => $currentAttributeValue) {
            $changedValue = $owner->getAttribute($attributeName);

            $changedValue = $this->processFloatValue($changedValue);
            $currentAttributeValue = $this->processFloatValue($currentAttributeValue);
            if ($changedValue !== $currentAttributeValue) {
                $changedInfo[$attributeName] = [$currentAttributeValue, $changedValue];
            }
        }

        return $changedInfo;
    }

    public function addLog(Event $event): void
    {
        /* @var ActiveRecord $owner */
        $owner = $this->owner;
        $changedAttributes = $this->unsetExcludedAttributes($event->changedAttributes);

        if (empty($changedAttributes)) {
            return;
        }

        $loggableData = $this->extractChangedAttributeValues($owner, $changedAttributes);

        if (empty($loggableData)) {
            return;
        }

        $loggerObject = new ArLoggerObject(
            get_class($owner),
            $owner->primaryKey,
            $loggableData,
            $this->setAction($event->name),
            method_exists($owner, 'getLogDescription') ? $owner->getLogDescription() : ""
        );
        $this->storage->store($loggerObject);
    }

    public function addDeleteLog()
    {
        /* @var ActiveRecord $owner */
        $owner = $this->owner;

        $loggerObject = new ArLoggerObject(
            get_class($owner),
            $owner->primaryKey,
            $owner->attributes,
            self::ACTION_DELETE,
            method_exists($owner, 'getLogDescription') ? $owner->getLogDescription() : ""
        );
        $this->storage->store($loggerObject);
    }
}