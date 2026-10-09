<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FieldstoneCrmTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    #[DataProvider('panelPages')]
    public function test_authenticated_user_can_view_page(string $path): void
    {
        $user = User::first();

        $this->actingAs($user)
            ->get($path)
            ->assertOk();
    }

    public static function panelPages(): array
    {
        return [
            'insights dashboard' => ['/'],
            'setup guide' => ['/setup-guide'],
            'people' => ['/contacts'],
            'organizations' => ['/organizations'],
            'activities' => ['/activities'],
            'deals' => ['/deals'],
            'leads' => ['/leads'],
        ];
    }
}
