<?php

namespace Tests\Feature;

use App\Livewire\TestDataGenerator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GenerateDataTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/generator');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_generator_page(): void
    {
        $response = $this->actingAs($this->user)->get('/generator');
        $response->assertStatus(200);
        $response->assertSee('Generator Data');
    }

    public function test_validation_fails_on_empty_category(): void
    {
        Livewire::actingAs($this->user)
            ->test(TestDataGenerator::class)
            ->set('category', '')
            ->set('quantity', 100)
            ->call('generate')
            ->assertHasErrors(['category' => 'required']);
    }

    public function test_validation_fails_on_invalid_category(): void
    {
        Livewire::actingAs($this->user)
            ->test(TestDataGenerator::class)
            ->set('category', 'invalid_cat')
            ->set('quantity', 100)
            ->call('generate')
            ->assertHasErrors(['category' => 'in']);
    }

    public function test_validation_fails_on_quantity_bounds(): void
    {
        Livewire::actingAs($this->user)
            ->test(TestDataGenerator::class)
            ->set('category', 'pegawai')
            ->set('quantity', 0)
            ->call('generate')
            ->assertHasErrors(['quantity' => 'min']);

        Livewire::actingAs($this->user)
            ->test(TestDataGenerator::class)
            ->set('category', 'pegawai')
            ->set('quantity', 10001)
            ->call('generate')
            ->assertHasErrors(['quantity' => 'max']);
    }

    public function test_successful_data_generation(): void
    {
        Livewire::actingAs($this->user)
            ->test(TestDataGenerator::class)
            ->set('category', 'pegawai')
            ->set('quantity', 25)
            ->call('generate')
            ->assertHasNoErrors()
            ->assertSet('hasData', true)
            ->assertSet('totalRows', 25);
    }
}
