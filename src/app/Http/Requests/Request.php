<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Responses\ValidationErrorResponse;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    /**
     * @phpstan-return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @phpstan-return array<string, ValidationRule|array<mixed>|string>
     */
    abstract public function rules(): array;

    /**
     * @phpstan-param Validator $validator
     * @phpstan-return void
     */
    protected function failedValidation(Validator $validator): void
    {
        $validationErrorResponse = new ValidationErrorResponse(
            messageBag: $validator->errors()
        );

        throw new HttpResponseException(
            response: $validationErrorResponse->toResponse(request: $this)
        );
    }
}