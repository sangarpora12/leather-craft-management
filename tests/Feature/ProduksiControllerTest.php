<?php

namespace Tests\Feature;

use App\Models\ProdukJadi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProduksiControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_halaman_perintah_produksi_dapat_diakses()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/produksi/create');
        $response->assertStatus(200);
        $response->assertViewIs('admin.produksi.create');
    }

    /** @test */
    public function test_validasi_form_spk_bekerja_dengan_benar()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/admin/produksi', [
            'operator_id' => '',
            'tanggal_target' => '',
            'products' => []
        ]);

        $response->assertSessionHasErrors(['operator_id', 'tanggal_target']);
    }

    /** @test */
    public function test_produk_tidak_siap_menampilkan_modal()
    {
        $produk = ProdukJadi::factory()->create([
            'is_ready' => false,
            'reason' => 'Bahan kurang'
        ]);

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/produksi/create');
        $response->assertSee('Produk Tidak Siap');
        $response->assertSee('Bahan kurang');
    }
}