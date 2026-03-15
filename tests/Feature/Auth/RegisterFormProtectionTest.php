<?php

namespace Tests\Feature\Auth;

use App\Mail\VerificationCodeMailable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterFormProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_endpoint_accepts_request_from_register_form(): void
    {
        Mail::fake();

        $this->get('/register')
            ->assertOk()
            ->assertSee('register-form-token');

        $response = $this->withHeaders($this->registerHeaders())
            ->postJson('/api/v1/register', $this->validPayload('protected-success@example.com'));

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'email' => 'protected-success@example.com',
                ],
            ]);

        $this->assertDatabaseHas('recruting_student', [
            'email' => 'protected-success@example.com',
            'status' => 'registered',
            'enabled' => 0,
        ]);

        $studentId = DB::table('recruting_student')
            ->where('email', 'protected-success@example.com')
            ->value('id');

        $this->assertDatabaseHas('gls_documents', [
            'student_id' => $studentId,
            'doc_status' => 'new',
        ]);

        Mail::assertSent(VerificationCodeMailable::class, 1);
    }

    public function test_register_endpoint_rejects_request_without_form_token(): void
    {
        $this->get('/register')->assertOk();

        $headers = $this->registerHeaders();
        unset($headers['X-Register-Form-Token']);

        $this->withHeaders($headers)
            ->postJson('/api/v1/register', $this->validPayload('protected-denied@example.com'))
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid register form token.',
            ]);

        $this->assertDatabaseMissing('recruting_student', [
            'email' => 'protected-denied@example.com',
        ]);
    }

    private function registerHeaders(): array
    {
        $baseUrl = rtrim(url('/'), '/');

        return [
            'X-CSRF-TOKEN' => csrf_token(),
            'X-Register-Form-Token' => (string) session('register_form_token'),
            'Origin' => $baseUrl,
            'Referer' => $baseUrl . '/register',
            'X-Requested-With' => 'XMLHttpRequest',
        ];
    }

    private function validPayload(string $email): array
    {
        return [
            'email' => $email,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'name' => 'Jan',
            'surname' => 'Kowalski',
            'dob' => '2014-09-01',
            'parent_name' => 'Anna',
            'parent_surname' => 'Kowalska',
            'parent_phone' => '+48123456789',
            'country' => 'PL',
            'city' => 'Warszawa',
            'address' => 'Marszałkowska 1',
            'zip' => '00-001',
            'apartment' => '12',
            'photo_consent' => 1,
            'terms_accepted' => 1,
            'privacy_accepted' => 1,
            'data_processing' => 1,
            'urgent_start' => 0,
            'recording_consent' => 0,
            'marketing_consent' => 0,
            'reg_comment' => 'Protected flow',
            'language' => 'pl',
        ];
    }
}

