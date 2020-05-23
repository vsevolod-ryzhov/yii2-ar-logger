# Logger behavior for Yii2 Active Record

Log change of Yii2 AR models

## Installation

Via Composer
```
composer require vsevolod-ryzhov/yii2-ar-logger
```

## Logs storage
You can install migration to store data in your database:
```
php yii migrate --migrationPath=@vendor/vsevolod-ryzhov/yii2-ar-logger/src/migrations
```
or you can use your own storage data class, which should implement *ArLoggerStorageInterface* interface

## Usage
Update your ActiveRecord class:
- if you use the built-in class *DbLoggerStorage*
```php
public function behaviors()
{
    return [
        ...
        [
            'class' => ArLoggerBehavior::class,
            'excludedAttributes' => ['created_at', 'updated_at'],
        ],
        ...
    ];
}
```
- if you want to use your own storage:
```php
// create your own storage class
class MyOwnStorage implements ArLoggerStorageInterface
{
    public function store(ArLoggerObject $object): bool
    {
        // save $object here
        return true;
    }
}

// pass storage class to behavior param "storage"
public function behaviors()
{
    return [
        ...
        [
            'class' => ArLoggerBehavior::class,
            'storage' => MyOwnStorage::class,
            'excludedAttributes' => ['created_at', 'updated_at'],
        ],
        ...
    ];
}
```