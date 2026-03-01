<?php

namespace Tests\Feature\Console\Commands;

use Tests\TestCase;
use App\Models\CompanyDocument;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

class UpdateExpiredDocumentsTest extends TestCase
{
    use RefreshDatabase;

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SIGNED = 'signed';
    const STATUS_EXPIRED = 'expired';

    /** @test */
    public function it_updates_expired_pending_documents_to_expired()
    {
        $this->withoutExceptionHandling();

        Log::info('Starting test: it_updates_expired_pending_documents_to_expired');

        $company = Company::factory()->create();

        // 1. ✅ গতকাল expire - pending (আপডেট হবে)
        $doc1 = CompanyDocument::create([
            'company_id' => $company->id,
            'name' => 'Expired Yesterday Document',
            'file_path' => 'documents/test1.pdf',
            'status' => self::STATUS_PENDING,
            'expires_at' => Carbon::yesterday(),
        ]);

        // 2. আজকে expire - pending (আপডেট হবে না)
        $doc2 = CompanyDocument::create([
            'company_id' => $company->id,
            'name' => 'Expires Today Document',
            'file_path' => 'documents/test2.pdf',
            'status' => self::STATUS_PENDING,
            'expires_at' => Carbon::today(),
        ]);

        // 3. আগামীকাল expire - pending (আপডেট হবে না)
        $doc3 = CompanyDocument::create([
            'company_id' => $company->id,
            'name' => 'Expires Tomorrow Document',
            'file_path' => 'documents/test3.pdf',
            'status' => self::STATUS_PENDING,
            'expires_at' => Carbon::tomorrow(),
        ]);

        // 4. expires_at null - pending (আপডেট হবে না)
        $doc4 = CompanyDocument::create([
            'company_id' => $company->id,
            'name' => 'No Expiry Document',
            'file_path' => 'documents/test4.pdf',
            'status' => self::STATUS_PENDING,
            'expires_at' => null,
        ]);

        // 5. status signed - expires_at গতকাল (আপডেট হবে না)
        $doc5 = CompanyDocument::create([
            'company_id' => $company->id,
            'name' => 'Signed Document',
            'file_path' => 'documents/test5.pdf',
            'status' => self::STATUS_SIGNED,
            'expires_at' => Carbon::yesterday(),
        ]);

        Log::info('Before command:', [
            'doc1' => $doc1->status,
            'doc2' => $doc2->status,
            'doc3' => $doc3->status,
            'doc4' => $doc4->status,
            'doc5' => $doc5->status,
        ]);

        // কমান্ড রান
        Artisan::call('documents:update-expired');
        $output = Artisan::output();

        // ডাটাবেস রিফ্রেশ
        $doc1->refresh();
        $doc2->refresh();
        $doc3->refresh();
        $doc4->refresh();
        $doc5->refresh();

        Log::info('After command:', [
            'doc1' => $doc1->status,
            'doc2' => $doc2->status,
            'doc3' => $doc3->status,
            'doc4' => $doc4->status,
            'doc5' => $doc5->status,
        ]);

        // Assertions
        $this->assertEquals(self::STATUS_EXPIRED, $doc1->status);
        $this->assertEquals(self::STATUS_PENDING, $doc2->status); // আজকে expire = pending
        $this->assertEquals(self::STATUS_PENDING, $doc3->status);
        $this->assertEquals(self::STATUS_PENDING, $doc4->status);
        $this->assertEquals(self::STATUS_SIGNED, $doc5->status); // signed unchanged

        $this->assertStringContainsString('Total documents updated to expired: 1', $output);
    }

    /** @test */
    public function it_only_updates_documents_with_pending_status()
    {
        $this->withoutExceptionHandling();

        $company = Company::factory()->create();
        $yesterday = Carbon::yesterday();

        // pending (আপডেট হবে)
        $pending = CompanyDocument::create([
            'company_id' => $company->id,
            'name' => 'Pending Document',
            'file_path' => 'documents/pending.pdf',
            'status' => self::STATUS_PENDING,
            'expires_at' => $yesterday,
        ]);

        // signed (আপডেট হবে না)
        $signed = CompanyDocument::create([
            'company_id' => $company->id,
            'name' => 'Signed Document',
            'file_path' => 'documents/signed.pdf',
            'status' => self::STATUS_SIGNED,
            'expires_at' => $yesterday,
        ]);

        // expired (আপডেট হবে না)
        $expired = CompanyDocument::create([
            'company_id' => $company->id,
            'name' => 'Expired Document',
            'file_path' => 'documents/expired.pdf',
            'status' => self::STATUS_EXPIRED,
            'expires_at' => $yesterday,
        ]);

        Artisan::call('documents:update-expired');
        $output = Artisan::output();

        $pending->refresh();
        $signed->refresh();
        $expired->refresh();

        $this->assertEquals(self::STATUS_EXPIRED, $pending->status);
        $this->assertEquals(self::STATUS_SIGNED, $signed->status);
        $this->assertEquals(self::STATUS_EXPIRED, $expired->status);

        $this->assertStringContainsString('Total documents updated to expired: 1', $output);
    }

    /** @test */
    public function it_handles_multiple_expired_documents()
    {
        $this->withoutExceptionHandling();

        $company = Company::factory()->create();

        // ৫টি expired document (expires_at অতীতে)
        for ($i = 1; $i <= 5; $i++) {
            CompanyDocument::create([
                'company_id' => $company->id,
                'name' => "Expired Document {$i}",
                'file_path' => "documents/expired{$i}.pdf",
                'status' => self::STATUS_PENDING,
                'expires_at' => Carbon::now()->subDays($i),
            ]);
        }

        // ২টি valid document (expires_at ভবিষ্যতে)
        for ($i = 1; $i <= 2; $i++) {
            CompanyDocument::create([
                'company_id' => $company->id,
                'name' => "Valid Document {$i}",
                'file_path' => "documents/valid{$i}.pdf",
                'status' => self::STATUS_PENDING,
                'expires_at' => Carbon::now()->addDays($i),
            ]);
        }

        // ২টি signed document (expires_at অতীতে - আপডেট হবে না)
        for ($i = 1; $i <= 2; $i++) {
            CompanyDocument::create([
                'company_id' => $company->id,
                'name' => "Signed Document {$i}",
                'file_path' => "documents/signed{$i}.pdf",
                'status' => self::STATUS_SIGNED,
                'expires_at' => Carbon::yesterday(),
            ]);
        }

        Artisan::call('documents:update-expired');
        $output = Artisan::output();

        $expiredCount = CompanyDocument::where('status', self::STATUS_EXPIRED)->count();
        $pendingCount = CompanyDocument::where('status', self::STATUS_PENDING)->count();
        $signedCount = CompanyDocument::where('status', self::STATUS_SIGNED)->count();

        Log::info('Counts:', [
            'expired' => $expiredCount,
            'pending' => $pendingCount,
            'signed' => $signedCount
        ]);

        // ৫টি expired document + ১টি pending থেকে expired হয়েছে = ৬?
        // না, কারণ pending থেকে expired হয়েছে ৫টি, signed গুলো unchanged
        $this->assertEquals(5, $expiredCount); // ৫টি pending expired হয়েছে
        $this->assertEquals(2, $pendingCount); // ২টি pending (ভবিষ্যতে expire)
        $this->assertEquals(2, $signedCount);  // ২টি signed (unchanged)

        $this->assertStringContainsString('Total documents updated to expired: 5', $output);
    }

    /** @test */
    public function it_handles_boundary_conditions_correctly()
    {
        $this->withoutExceptionHandling();

        $company = Company::factory()->create();
        $today = Carbon::today();

        // গতকাল ২৩:৫৯:৫৯ - expired হওয়া উচিত
        $doc1 = CompanyDocument::create([
            'company_id' => $company->id,
            'name' => 'Boundary Test 1',
            'file_path' => 'documents/boundary1.pdf',
            'status' => self::STATUS_PENDING,
            'expires_at' => $today->copy()->subSecond(),
        ]);

        // আজকে ০০:০০:০০ - pending থাকা উচিত
        $doc2 = CompanyDocument::create([
            'company_id' => $company->id,
            'name' => 'Boundary Test 2',
            'file_path' => 'documents/boundary2.pdf',
            'status' => self::STATUS_PENDING,
            'expires_at' => $today->copy()->startOfDay(),
        ]);

        Artisan::call('documents:update-expired');
        $output = Artisan::output();

        $doc1->refresh();
        $doc2->refresh();

        $this->assertEquals(self::STATUS_EXPIRED, $doc1->status);
        $this->assertEquals(self::STATUS_PENDING, $doc2->status);
        $this->assertStringContainsString('Total documents updated to expired: 1', $output);
    }

    /** @test */
    public function it_returns_success_code()
    {
        $exitCode = Artisan::call('documents:update-expired');
        $this->assertEquals(Command::SUCCESS, $exitCode);
    }

    /** @test */
    public function it_handles_empty_documents_table()
    {
        $exitCode = Artisan::call('documents:update-expired');
        $output = Artisan::output();

        $this->assertEquals(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Total documents updated to expired: 0', $output);
    }
}
