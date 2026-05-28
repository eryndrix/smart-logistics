<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

final class RoleSeeder extends Seeder
{
    /**
     * Array of predefined roles (localized names, English slugs)
     * 
     * @var array<array{name: string, slug: string}>
     */
    private array $roles = [
        [
            'name' => 'Администратор',
            'slug' => 'admin'
        ],
        [
            'name' => 'Пользователь', 
            'slug' => 'user'
        ],
        [
            'name' => 'Гость',
            'slug' => 'guest'
        ],
    ];

    /**
     * Run the database seeds for roles table.
     */
    public function run(): void
    {
        $data = array_map(
            callback: fn (array $role): array => [
                'name' => $role['name'],
                'slug' => $role['slug'],
                'created_at' => $now = Carbon::now(),
                'updated_at' => $now
            ],
            array: $this->roles
        );

        DB::table(table: 'roles')->insert(values: $data);
    }
}
