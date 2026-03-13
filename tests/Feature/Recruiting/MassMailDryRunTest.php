<?php

namespace Tests\Feature\Recruiting;

use Tests\TestCase;
use App\Models\RecruitingCampaign;
use App\Models\RecruitingStudentImport;
use App\Services\Recruiting\MassMailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MassMailDryRunTest extends TestCase
{
    use RefreshDatabase;

    public function test_dry_run_validates_emails_and_finds_duplicates()
    {
        // Arrange: create campaign with mixed data
        $campaign = RecruitingCampaign::create([
            'name' => 'Test Campaign',
            'email_subject' => 'Hello',
            'email_template' => 'test',
            'status' => 'draft',
        ]);

        RecruitingStudentImport::create([
            'email' => 'valid@test.pl',
            'campaign_id' => $campaign->id,
            'token' => Str::random(48),
            'status' => 'pending',
        ]);

        RecruitingStudentImport::create([
            'email' => 'invalid-email',
            'campaign_id' => $campaign->id,
            'token' => Str::random(48),
            'status' => 'pending',
        ]);

        // Insert existing student manually into recruting_student
        DB::table('recruting_student')->insert([
            'email' => 'existing@test.pl',
            'name' => 'Existing',
            'surname' => 'Student',
            'password' => 'secret',
            'phone' => '',
            'subject' => '',
            'status' => 'new',
            'enabled' => 1,
            'blocked' => 0,
            'deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        RecruitingStudentImport::create([
            'email' => 'existing@test.pl',
            'campaign_id' => $campaign->id,
            'token' => Str::random(48),
            'status' => 'pending',
        ]);

        // Act
        $stats = (new MassMailService())->dryRun($campaign);

        // Assert
        $this->assertEquals(2, $stats['valid_emails']);
        $this->assertEquals(1, $stats['invalid_emails']);
        $this->assertEquals(1, $stats['duplicate_in_students']);
        $this->assertEquals(1, $stats['ready_to_send']);
    }

    public function test_send_job_does_not_send_in_dry_run_mode()
    {
        Mail::fake();
        
        $campaign = RecruitingCampaign::create([
            'name' => 'Test Campaign',
            'email_subject' => 'Hello',
            'email_template' => 'test',
            'status' => 'draft',
        ]);

        $stats = (new MassMailService())->dryRun($campaign);
        
        Mail::assertNothingSent();
    }

    public function test_invite_link_converts_import_to_student()
    {
        // Ensure the table exists for testing if RefreshDatabase didn't pick it up 
        // (usually it does for migrations, but just in case)
        if (!Schema::hasTable('recruting_student')) {
            Schema::create('recruting_student', function ($table) {
                $table->id();
                $table->string('email');
                $table->string('name');
                $table->string('surname');
                $table->string('phone');
                $table->string('subject');
                $table->string('status');
                $table->string('password');
                $table->boolean('enabled');
                $table->boolean('blocked');
                $table->boolean('deleted');
                $table->timestamps();
            });
        }

        $import = RecruitingStudentImport::create([
            'token' => 'test-token-123',
            'email' => 'new@test.pl',
            'name' => 'New',
            'surname' => 'Student',
            'status' => 'sent',
        ]);

        $response = $this->get('/register/invite/test-token-123');

        $response->assertRedirect();
        $this->assertEquals('converted', $import->fresh()->status);
        $this->assertTrue(DB::table('recruting_student')->where('email', 'new@test.pl')->exists());
    }
}
