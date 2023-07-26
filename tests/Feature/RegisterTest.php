<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    public function test_auth_user_can_not_register(): void
    {
        $response = $this->actingAs(User::factory()->create(), 'sanctum')
            ->postJson(route('api.register'));

        $response
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(['error' => true]);
    }

    public function test_register_validating_name_correctly(): void
    {
        // required
        $response = $this->postJson(route('api.register'));

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('name');

        // string
        $response = $this->postJson(route('api.register'), [
            'name' => UploadedFile::fake()->create('file.pdf')
        ]);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('name');

        // length
        $response = $this->postJson(route('api.register'), [
            'name' => Str::random(500)
        ]);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('name');

        // valid
        $response = $this->postJson(route('api.register'), [
            'name' => fake()->name()
        ]);

        $response->assertJsonMissingValidationErrors('name');
    }

    public function test_register_validating_email_correctly(): void
    {
        // required
        $response = $this->postJson(route('api.register'));

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('email');

        // email
        $response = $this->postJson(route('api.register'), [
            'email' => "John Doe"
        ]);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('email');

        // uniqueness
        $user = User::factory()->create();
        $response = $this->postJson(route('api.register'), [
            'email' => $user->email
        ]);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('email');

        // valid
        $response = $this->postJson(route('api.register'), [
            'email' => fake()->email()
        ]);

        $response->assertJsonMissingValidationErrors('email');
    }

    public function test_register_validating_password_correctly(): void
    {
        // required
        $response = $this->postJson(route('api.register'));

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('password');

        // string
        $response = $this->postJson(route('api.register'), [
            'password' => UploadedFile::fake()->create('file.pdf')
        ]);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('password');

        // minimum is 8
        $response = $this->postJson(route('api.register'), [
            'password' => Str::random(5)
        ]);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('password');

        // confirmed
        $response = $this->postJson(route('api.register'), [
            'password' => 'secret123'
        ]);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrorFor('password');

        // valid
        $response = $this->postJson(route('api.register'), [
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertJsonMissingValidationErrors('password');
    }
}
