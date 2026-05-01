<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MhsYud;
use App\Models\Yudicium;
use App\Models\Mahasiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class MahasiswaYudisiumTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test yudicium
        Yudicium::create([
            'no_yudicium' => 'YUD-001',
            'fakultas_id' => 1,
            'prodi_id' => 12345,
            'periode' => '20241',
            'approval_status' => 'pending'
        ]);
    }

    /**
     * Test filter mahasiswa dari API
     */
    public function test_filter_mahasiswa_from_api()
    {
        // Mock API response
        Http::fake([
            '*' => Http::response([
                [
                    'nim' => '1234567890',
                    'nama' => 'John Doe',
                    'masa_studi' => 8,
                    'sks_lulus' => 144,
                    'ipk' => 3.75,
                    'predikat' => 'Dengan Pujian (Cumlaude)',
                    'status' => 'Eligible'
                ]
            ], 200)
        ]);

        $response = $this->postJson('/yudisium/filter-mahasiswa', [
            'prodi' => '12345',
            'periode' => '20241'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'source' => 'api'
                 ])
                 ->assertJsonStructure([
                     'success',
                     'source',
                     'data' => [
                         '*' => [
                             'nim',
                             'nama',
                             'masa_studi',
                             'sks_lulus',
                             'ipk',
                             'predikat',
                             'status',
                             'source'
                         ]
                     ]
                 ]);
    }

    /**
     * Test filter mahasiswa fallback ke database
     */
    public function test_filter_mahasiswa_fallback_to_database()
    {
        // Create test mahasiswa
        Mahasiswa::create([
            'STUDENTID' => '1234567890',
            'FULLNAME' => 'Jane Smith',
            'MASA_STUDI' => 7,
            'PASS_CREDIT' => 140,
            'GPA' => 3.50,
            'PREDIKAT' => 'Sangat Memuaskan (Very Good)',
            'STATUS' => 'Eligible',
            'STUDYPROGRAMID' => '12345'
        ]);

        // Mock API failure
        Http::fake([
            '*' => Http::response([], 500)
        ]);

        $response = $this->postJson('/yudisium/filter-mahasiswa', [
            'prodi' => '12345',
            'periode' => '20241'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'source' => 'database'
                 ]);
    }

    /**
     * Test simpan draft dengan source API
     */
    public function test_simpan_draft_with_api_source()
    {
        // Mock API response
        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        $response = $this->postJson('/yudisium/simpan-draft', [
            'yudicium_id' => 1,
            'periode' => '20241',
            'mahasiswa' => [
                [
                    'nim' => '1234567890',
                    'nama' => 'John Doe',
                    'masa_studi' => 8,
                    'sks_lulus' => 144,
                    'ipk' => 3.75,
                    'predikat' => 'Dengan Pujian (Cumlaude)',
                    'source' => 'api'
                ]
            ]
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ]);
    }

    /**
     * Test simpan draft dengan source database
     */
    public function test_simpan_draft_with_database_source()
    {
        $response = $this->postJson('/yudisium/simpan-draft', [
            'yudicium_id' => 1,
            'periode' => '20241',
            'mahasiswa' => [
                [
                    'nim' => '1234567890',
                    'nama' => 'John Doe',
                    'masa_studi' => 8,
                    'sks_lulus' => 144,
                    'ipk' => 3.75,
                    'predikat' => 'Dengan Pujian (Cumlaude)',
                    'source' => 'database'
                ]
            ]
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ]);

        // Verify data inserted
        $this->assertDatabaseHas('mhs_yudiciums', [
            'nim' => '1234567890',
            'yudicium_id' => 1,
            'status' => 'draft'
        ]);
    }

    /**
     * Test get draft dari database
     */
    public function test_get_draft_from_database()
    {
        // Create draft data
        MhsYud::create([
            'nim' => '1234567890',
            'yudicium_id' => 1,
            'name' => 'John Doe',
            'study_period' => 8,
            'pass_sks' => 144,
            'ipk' => 3.75,
            'predikat' => 'Dengan Pujian (Cumlaude)',
            'status' => 'draft'
        ]);

        $response = $this->getJson('/yudisium/get-draft?yudicium_id=1&periode=20241&source=database');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'source' => 'database'
                 ])
                 ->assertJsonCount(1, 'data');
    }

    /**
     * Test tetapkan yudisium dengan source database
     */
    public function test_tetapkan_yudisium_with_database_source()
    {
        // Create draft data
        MhsYud::create([
            'nim' => '1234567890',
            'yudicium_id' => 1,
            'name' => 'John Doe',
            'study_period' => 8,
            'pass_sks' => 144,
            'ipk' => 3.75,
            'predikat' => 'Dengan Pujian (Cumlaude)',
            'status' => 'draft'
        ]);

        $response = $this->postJson('/yudisium/tetapkan-yudisium', [
            'yudicium_id' => 1,
            'periode' => '20241',
            'source' => 'database'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'source' => 'database'
                 ]);

        // Verify status updated to final
        $this->assertDatabaseHas('mhs_yudiciums', [
            'nim' => '1234567890',
            'yudicium_id' => 1,
            'status' => 'final'
        ]);
    }

    /**
     * Test tetapkan yudisium dengan source API
     */
    public function test_tetapkan_yudisium_with_api_source()
    {
        // Mock API response
        Http::fake([
            '*' => Http::response([
                [
                    'nim' => '1234567890',
                    'nama' => 'John Doe',
                    'masa_studi' => 8,
                    'sks_lulus' => 144,
                    'ipk' => 3.75,
                    'predikat' => 'Dengan Pujian (Cumlaude)',
                    'status' => 'Eligible'
                ]
            ], 200)
        ]);

        $response = $this->postJson('/yudisium/tetapkan-yudisium', [
            'yudicium_id' => 1,
            'periode' => '20241',
            'source' => 'api'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'source' => 'api'
                 ]);

        // Verify data inserted with final status
        $this->assertDatabaseHas('mhs_yudiciums', [
            'nim' => '1234567890',
            'yudicium_id' => 1,
            'status' => 'final'
        ]);
    }

    /**
     * Test duplicate prevention
     */
    public function test_duplicate_prevention()
    {
        // Create existing data
        MhsYud::create([
            'nim' => '1234567890',
            'yudicium_id' => 1,
            'name' => 'John Doe',
            'study_period' => 8,
            'pass_sks' => 144,
            'ipk' => 3.75,
            'predikat' => 'Dengan Pujian (Cumlaude)',
            'status' => 'draft'
        ]);

        // Try to insert duplicate
        $response = $this->postJson('/yudisium/simpan-draft', [
            'yudicium_id' => 1,
            'periode' => '20241',
            'mahasiswa' => [
                [
                    'nim' => '1234567890',
                    'nama' => 'John Doe',
                    'masa_studi' => 8,
                    'sks_lulus' => 144,
                    'ipk' => 3.75,
                    'predikat' => 'Dengan Pujian (Cumlaude)',
                    'source' => 'database'
                ]
            ]
        ]);

        $response->assertStatus(200);

        // Verify only one record exists
        $count = MhsYud::where('nim', '1234567890')
                       ->where('yudicium_id', 1)
                       ->count();
        
        $this->assertEquals(1, $count);
    }

    /**
     * Test validation errors
     */
    public function test_validation_errors()
    {
        // Missing required fields
        $response = $this->postJson('/yudisium/simpan-draft', [
            'yudicium_id' => 1
            // Missing periode and mahasiswa
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['periode', 'mahasiswa']);
    }

    /**
     * Test hapus draft API
     */
    public function test_hapus_draft_api()
    {
        // Mock API response
        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        $response = $this->postJson('/yudisium/hapus-draft-api', [
            'nim' => '1234567890',
            'periode' => '20241'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ]);
    }
}
