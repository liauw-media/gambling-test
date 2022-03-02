<?php

namespace Tests\Feature;

use App\Models\Affiliate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AffiliateTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @test
     */
    public function affiliateListCanBeLoaded()
    {
        Affiliate::factory(20)->create();
        $response = $this->get('/');
        $response->assertStatus(200);
        $this->assertDatabaseCount(table: 'affiliates', count: 20);
    }

    /**
     * @test
     */
    public function userCanUploadAffiliateJsonFile()
    {
        $this->withoutExceptionHandling();
        Storage::fake('testing');

        $filename = public_path('tests/Updated affiliates.txt');
        $file = new UploadedFile($filename, 'list.txt', 'plain/txt', null, true);

        $this->json('POST', '/', [
            'file' => $file,
        ]);
        $this->assertDatabaseCount(table: 'affiliates', count: 32);
    }

    /**
     * @test
     */
    public function userCanTryUploadInvalidFile()
    {
        $this->withoutExceptionHandling();
        Storage::fake('testing');

        $filename = public_path('tests/php.txt');
        $file = new UploadedFile($filename, 'list.txt', 'plain/txt', null, true);

        $this->json('POST', '/', [
            'file' => $file,
        ]);
        $this->assertDatabaseCount(table: 'affiliates', count: 0);
    }
}
