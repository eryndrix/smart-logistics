<?php declare(strict_types=1);

namespace App\Http\Requests;

final class MessageRequest extends Request
{
    /**
     * @phpstan-return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @phpstan-return array{
     *   channel: array<int, string>,
     *   message: array<int, string>,
     *   recipient_ids: array<int, string>,
     *   'recipient_ids.*': array<int, string>,
     *   priority: array<int, string>
     * }
     */
    public function rules(): array
    {
        return [
            'channel' => [
                'bail',
                'required',
                'string:strict',
                'in:sms,mail',
            ],
            'message' => [
                'bail',
                'required',
                'string:strict',
                'min:8',
                'max:254',
            ],
            'recipient_ids' => [
                'bail',
                'array',
                'min:1',
            ],
            'recipient_ids.*' => [
                'bail',
                'uuid',
            ],
            'priority' => [
                'bail',
                'string:strict',
                'in:urgent,normal',
            ],
        ];
    }

    /**
     * @phpstan-return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'priority' => $this->route('priority'),
        ]);
    }
}
