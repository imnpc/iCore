<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UploadEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_upload_a_valid_image(): void
    {
        Storage::fake('public');
        Sanctum::actingAs(User::factory()->create());

        $response = $this->post('/api/v1/utils/upload', [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
            'type' => 0,
            'tag' => 'avatar',
        ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $filePath = (string) $response->json('data.file_path');
        $this->assertNotSame('', $filePath);
        $this->assertTrue(Storage::disk('public')->exists($filePath));
    }

    public function test_upload_rejects_an_unknown_type(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->post('/api/v1/utils/upload', [
            'file' => UploadedFile::fake()->image('avatar.jpg'),
            'type' => 3,
        ], ['Accept' => 'application/json'])
            ->assertUnprocessable();
    }

    public function test_upload_rejects_svg_images(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->post('/api/v1/utils/upload', [
            'file' => UploadedFile::fake()->create('avatar.svg', 1, 'image/svg+xml'),
            'type' => 0,
        ], ['Accept' => 'application/json'])
            ->assertUnprocessable();
    }

    public function test_debug_token_endpoint_is_not_exposed(): void
    {
        $this->getJson('/api/v1/utils/getUserToken?user_id=1')
            ->assertNotFound();
    }
}
