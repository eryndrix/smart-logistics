<?php declare(strict_types=1);

namespace App\Shared\Traits;

use Illuminate\Support\Str;

/**
 * @phpstan-template TModel of object
 * @mixin TModel
 */
trait CamelCaseAttributes
{
    /**
     * @phpstan-param string $key
     * @phpstan-return mixed
     */
    public function getAttribute($key)
    {
        if (array_key_exists(key: $key,
            array: $this->relations
        )) {
            return $this->relations[$key];
        }

        $snakeKey = Str::snake(value: $key);

        if ($this->hasAttribute(key: $snakeKey)) {
            return parent::getAttribute(
                key: $snakeKey
            );
        }

        return parent::getAttribute(key: $key);
    }

    /**
     * @phpstan-param string $key
     * @phpstan-param mixed $value
     *
     * @phpstan-return $this
     */
    public function setAttribute($key, $value)
    {
        $snakeKey = Str::snake(value: $key);

        parent::setAttribute(
            key: $snakeKey,
            value: $value
        );

        return $this;
    }

    /**
     * @phpstan-param string $key
     * @phpstan-return bool
     */
    public function hasAttribute($key): bool
    {
        return array_key_exists(
            key: $key,
            array: $this->attributes
        ) || array_key_exists(
            key: $key,
            array: $this->casts
        ) || method_exists(
            object_or_class: $this,
            method: Str::camel(value: $key)
        );
    }
}
