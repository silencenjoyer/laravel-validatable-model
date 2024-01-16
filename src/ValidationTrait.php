<?php

namespace Silencenjoyer\LaravelValidatableModel;

use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

/**
 * Trait ValidationTrait provides possibility to validate a model using
 * its own validation rules.
 * This can be particularly useful if you need to reuse validation code or if
 * you want to make your controller code more concise.
 *
 * These capabilities could be extended to model lifecycle events.
 * For example, validating on save:
 * ```php
 * class MyModel extends Model
 * {
 *     ...
 *     protected static function boot()
 *     {
 *         parent::boot();
 *         static::saving(function ($model) {
 *             $model->validate();
 *         });
 *     }
 * }
 * ```
 *
 * @package App\Models
 */
trait ValidationTrait
{
    /**
     * Storage for current model's validation errors.
     * It will be populated with data if {@see validate()} is called with
     * 'throwException' parameter set to false.
     *
     * ```php
     * $model->validate(false);
     * ```
     *
     * @var MessageBag
     */
    protected MessageBag $errors;

    /**
     * Method for defining model validation rules.
     * This could provide code reuse and lessening controller/s size.
     *
     * @return array<string, string|array>
     */
    abstract public function rules(): array;

    /**
     * Provides data for validation.
     * For example, it may include filtering attributes.
     *
     * ```php
     * public function fields(): array
     * {
     *     return $this->attributes;
     * }
     * ```
     *
     * @return array
     */
    abstract public function fields(): array;

    /**
     * Validates the attributes of the current model.
     *
     * @param bool $throwException
     * @return bool
     * @throws ValidationException
     */
    public function validate(bool $throwException = true): bool
    {
        $validator = Validator::make($this->fields(), $this->rules());
        $pass = $validator->passes();

        if (!$pass && $throwException) {
            throw new ValidationException($validator);
        }

        $this->errors = $validator->errors();
        return $pass;
    }

    /**
     * Provides an {@see MessageBag} of validation errors.
     * If no MessageBag set, new one empty MessageBag is returned.
     *
     * @return MessageBag
     */
    public function getErrors(): MessageBag
    {
        if (!isset($this->errors)) {
            $this->errors = new MessageBag();
        }

        return $this->errors;
    }
}
