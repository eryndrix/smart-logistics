<?php declare(strict_types=1);

namespace App\Domains\Account\Login;

use App\Shared\Command;
use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Casting\BooleanCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;

final class LoginCommand extends Command
{
    /**
     * @phpstan-var string
     */
    #[Cast(type: StringCast::class, param: null)]
    public string $email;

    /**
     * @phpstan-var string
     */
    #[Cast(type: StringCast::class, param: null)]
    public string $password;

    /**
     * @phpstan-var bool
     */
    #[Cast(type: BooleanCast::class, param: null)]
    public bool $rememberMe;

    /**
     * @phpstan-return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'email' => [
                'bail',
                'required',
                'email:rfc,strict,spoof',
                'max:254',
            ],
            'password' => [
                'bail',
                'required',
                'string',
                'min:8',
                'max:28',
            ],
            'rememberMe' => [
                'bail',
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * @phpstan-return array<string, mixed>
     */
    protected function defaults(): array
    {
        return ['rememberMe' => false];
    }

    /**
     * @phpstan-return array<string, string>
     */
    protected function mapData(): array
    {
        return [
            'remember_me' => 'rememberMe',
        ];
    }
}
