<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    public function test_auth_user_can_not_login(): void
    {
        $response = $this->actingAs(User::factory()->create(), 'sanctum')
            ->postJson(route('api.login'));

        $response
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(['error' => true]);
    }

    public function test_login_validating_email_correctly(): void
    {
        // required
        $response = $this->postJson(route('api.login'));

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('email');

        // email
        $response = $this->postJson(route('api.login'), [
            'email' => "John Doe"
        ]);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('email');

        // exists
        $response = $this->postJson(route('api.login'), [
            'email' => Str::random(16) . '@' . Str::random(5) . '.com'
        ]);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('email');

        // valid
        $user = User::factory()->create();
        $response = $this->postJson(route('api.login'), [
            'email' => $user->email
        ]);

        $response->assertJsonMissingValidationErrors('email');
    }

    public function test_login_validating_password_correctly(): void
    {
        // required
        $response = $this->postJson(route('api.login'));

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('password');

        // string
        $response = $this->postJson(route('api.login'), [
            'password' => UploadedFile::fake()->create('file.pdf')
        ]);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('password');

        // valid
        User::factory()->create(['password' => 'secret123']);
        $response = $this->postJson(route('api.login'), [
            'password' => 'secret123',
        ]);

        $response->assertJsonMissingValidationErrors('password');
    }
}
