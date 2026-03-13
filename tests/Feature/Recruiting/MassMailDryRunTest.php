<?php

namespace Tests\Feature\Recruiting;

use Tests\TestCase;
use App\Models\RecruitingCampaign;
use App\Models\RecruitingStudentImport;
use App\Services\Recruiting\MassMailService;
use App\Helpers\JwtHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MassMailDryRunTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to get Admin JWT headers.
     */
    protected function getAdminHeaders(): array
    {
        $payload = [
            'user_id' => 1,
            'email'   => 'admin@gls.pl',
            'role'    => 'admin',
            'guard'   => 'admin',
            'exp'     => time() + 3600,
        ];
        
        $token = JwtHelper::createToken($payload, config('app.key'));
        
        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];
    }

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

    public function test_invite_link_redirects_to_registration_completion()
    {
        $import = RecruitingStudentImport::create([
            'token' => 'test-token-123',
            'email' => 'new@test.pl',
            'name' => 'New',
            'surname' => 'Student',
            'status' => 'sent',
        ]);

        $response = $this->get('/register/invite/test-token-123');

        $response->assertRedirect(route('registration.complete', 'test-token-123'));
        $this->assertEquals('clicked', $import->fresh()->status);
    }

    public function test_registration_completion_converts_import_to_student_with_address()
    {
        // Setup tables for mapping in SQLite
        if (!Schema::hasTable('country')) {
            Schema::create('country', function ($table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('student')) {
            // Need a minimal student table for auth with proper fields
            Schema::create('student', function ($table) {
                $table->id();
                $table->string('email');
                $table->string('surname')->nullable();
                $table->string('lastname')->nullable();
                $table->string('phone')->nullable();
                $table->string('subject')->nullable();
                $table->integer('country_id')->nullable();
                $table->string('locality')->nullable();
                $table->string('password');
                $table->integer('enabled')->default(1);
                $table->integer('blocked')->default(0);
                $table->integer('deleted')->default(0);
                $table->timestamps();
            });
        }
        DB::table('country')->insert(['name' => 'Poland', 'id' => 173]);

        $import = RecruitingStudentImport::create([
            'token' => 'complete-token',
            'email' => 'complete@test.pl',
            'name' => 'John',
            'surname' => 'Doe',
            'country' => 'Poland',
            'city' => 'Warsaw',
            'address' => 'Street 1',
            'zip' => '00-001',
            'status' => 'clicked',
        ]);

        $response = $this->post(route('registration.complete.store', 'complete-token'), [
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'consent_data' => '1',
            'consent_policy' => '1',
            'consent_photo' => '1',
        ]);

        $response->assertStatus(302); // Redirect back on validation error or forward on success
        
        // Verify student data persistence
        $student = DB::table('recruting_student')->where('email', 'complete@test.pl')->first();
        $this->assertNotNull($student);
        $this->assertEquals('John', $student->name);
        $this->assertEquals('Warsaw', $student->locality);
        $this->assertEquals(173, $student->country_id);
        
        // Final state
        $this->assertEquals('converted', $import->fresh()->status);
    }

    public function test_api_returns_camel_case_json_with_jwt()
    {
        $campaign = RecruitingCampaign::create([
            'name' => 'Compliance Test',
            'email_subject' => 'Subject',
            'email_template' => 'template',
            'status' => 'draft',
            'total_count' => 10,
        ]);

        $response = $this->withHeaders($this->getAdminHeaders())
            ->getJson("/api/v1/recruiting/campaigns/{$campaign->id}/stats");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'status',
                'emailSubject',
                'emailTemplate',
                'totalCount',
                'sentCount',
                'failedCount',
                'clickedCount',
                'convertedCount',
                'createdAt',
            ]
        ]);
    }

    public function test_api_dry_run_returns_camel_case_json_with_jwt()
    {
        $campaign = RecruitingCampaign::create([
            'name' => 'Dry Run Test',
            'email_subject' => 'Subject',
            'email_template' => 'template',
            'status' => 'draft',
        ]);

        $response = $this->withHeaders($this->getAdminHeaders())
            ->postJson("/api/v1/recruiting/campaigns/{$campaign->id}/dry-run");

        $response->assertStatus(200);
        $response->assertJson([
            'validEmails' => 0,
            'invalidEmails' => 0,
            'duplicateInStudents' => 0,
            'readyToSend' => 0,
        ]);
    }

    public function test_api_unauthorized_without_jwt()
    {
        $response = $this->getJson("/api/v1/recruiting/campaigns");
        $response->assertStatus(401);
    }
}
