<?php

namespace Tests\Feature;

use App\Livewire\TestDataGenerator;
use App\Models\CustomTemplate;
use App\Models\GenerationHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserIsolationTest extends TestCase
{
    use RefreshDatabase;

    private User $userA;
    private User $userB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userA = User::factory()->create([
            'name' => 'Akun A',
            'email' => 'akuna@gmail.com',
            'google_id' => 'google-user-a',
        ]);

        $this->userB = User::factory()->create([
            'name' => 'Akun B',
            'email' => 'akunb@gmail.com',
            'google_id' => 'google-user-b',
        ]);
    }

    public function test_user_a_and_user_b_histories_are_isolated(): void
    {
        // User A generates data
        Livewire::actingAs($this->userA)
            ->test(TestDataGenerator::class)
            ->set('category', 'pegawai')
            ->set('quantity', 10)
            ->call('generate')
            ->assertSet('hasData', true);

        // User B generates data
        Livewire::actingAs($this->userB)
            ->test(TestDataGenerator::class)
            ->set('category', 'tagihan')
            ->set('quantity', 5)
            ->call('generate')
            ->assertSet('hasData', true);

        // User A should only see 1 history (theirs)
        $componentA = Livewire::actingAs($this->userA)->test(TestDataGenerator::class);
        $historiesA = $componentA->get('histories');
        $this->assertCount(1, $historiesA);
        $this->assertEquals('pegawai', $historiesA->first()->category);
        $this->assertEquals($this->userA->id, $historiesA->first()->user_id);

        // User B should only see 1 history (theirs)
        $componentB = Livewire::actingAs($this->userB)->test(TestDataGenerator::class);
        $historiesB = $componentB->get('histories');
        $this->assertCount(1, $historiesB);
        $this->assertEquals('tagihan', $historiesB->first()->category);
        $this->assertEquals($this->userB->id, $historiesB->first()->user_id);
    }

    public function test_user_b_cannot_delete_or_rerun_user_a_history(): void
    {
        $historyA = GenerationHistory::create([
            'user_id' => $this->userA->id,
            'category' => 'pegawai',
            'quantity' => 20,
            'mode' => 'normal',
        ]);

        // User B attempts to delete User A's history
        Livewire::actingAs($this->userB)
            ->test(TestDataGenerator::class)
            ->call('deleteHistory', $historyA->id);

        // History A must still exist
        $this->assertDatabaseHas('generation_histories', [
            'id' => $historyA->id,
            'user_id' => $this->userA->id,
        ]);

        // User B attempts to rerun User A's history
        $componentB = Livewire::actingAs($this->userB)
            ->test(TestDataGenerator::class)
            ->call('rerunFromHistory', $historyA->id);

        // Component should not load User A's category/hasData
        $componentB->assertSet('hasData', false);
    }

    public function test_clear_all_histories_only_clears_current_user_histories(): void
    {
        $historyA = GenerationHistory::create([
            'user_id' => $this->userA->id,
            'category' => 'pegawai',
            'quantity' => 15,
            'mode' => 'normal',
        ]);

        $historyB = GenerationHistory::create([
            'user_id' => $this->userB->id,
            'category' => 'murid',
            'quantity' => 25,
            'mode' => 'normal',
        ]);

        // User B clears all histories
        Livewire::actingAs($this->userB)
            ->test(TestDataGenerator::class)
            ->call('clearAllHistories');

        // User B's history is deleted
        $this->assertDatabaseMissing('generation_histories', [
            'id' => $historyB->id,
        ]);

        // User A's history remains safe
        $this->assertDatabaseHas('generation_histories', [
            'id' => $historyA->id,
            'user_id' => $this->userA->id,
        ]);
    }

    public function test_custom_templates_are_isolated_between_users(): void
    {
        $templateA = CustomTemplate::create([
            'user_id' => $this->userA->id,
            'name' => 'Template Rahasia A',
            'description' => 'Khusus user A',
            'columns' => [
                ['header' => 'Nama', 'key' => 'nama', 'type' => 'name', 'required' => true],
            ],
        ]);

        // User B should NOT see Template A
        $componentB = Livewire::actingAs($this->userB)->test(TestDataGenerator::class);
        $templatesB = $componentB->get('customTemplates');
        $this->assertCount(0, $templatesB);

        // User B cannot delete Template A
        $componentB->call('deleteCustomTemplate', $templateA->id);
        $this->assertDatabaseHas('custom_templates', [
            'id' => $templateA->id,
        ]);
    }

    public function test_user_b_cannot_export_user_a_session_data(): void
    {
        session([
            'generated_data' => [
                'user_id' => $this->userA->id,
                'category' => 'pegawai',
                'headers' => ['Nama'],
                'keys' => ['nama'],
                'data' => [['nama' => 'Budi']],
            ],
        ]);

        // User B tries to export CSV
        $response = $this->actingAs($this->userB)->post('/export/csv');
        $response->assertRedirect();
        $response->assertSessionHas('error');

        // User B tries to export Excel
        $responseExcel = $this->actingAs($this->userB)->post('/export/excel');
        $responseExcel->assertRedirect();
        $responseExcel->assertSessionHas('error');
    }
}

