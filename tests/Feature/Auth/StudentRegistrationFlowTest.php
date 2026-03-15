<?php

namespace Tests\Feature\Auth;

use App\Mail\VerificationCodeMailable;
use App\Models\RecrutingStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class StudentRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_student_can_resend_code_and_auto_login_after_successful_verification(): void
    {
        Mail::fake();

        $this->get('/register')
            ->assertOk()
            ->assertSee('Rejestracja');

        $payload = [
            'email' => 'student.flow@example.com',
            'password' => 'secret123',
            'name' => 'Jan',
            'surname' => 'Kowalski',
            'dob' => '2015-05-20',
            'parent_name' => 'Anna',
            'parent_surname' => 'Kowalska',
            'parent_phone' => '+48123456789',
            'country' => 'PL',
            'city' => 'Warszawa',
            'address' => 'Marszałkowska 1',
            'zip' => '00-001',
            'apartment' => '12A',
            'photo_consent' => 1,
            'terms_accepted' => 1,
            'privacy_accepted' => 1,
            'data_processing' => 1,
            'urgent_start' => 1,
            'recording_consent' => 1,
            'marketing_consent' => 1,
            'reg_comment' => 'Please contact after 18:00',
            'language' => 'pl',
        ];

        $firstCode = '111111';

        $studentModel = $this->createPendingStudent($payload, $firstCode);
        $this->clearStudentThrottle($payload['email']);

        // Открываем /verify чтобы получить verify_form_token в сессии
        $this->get('/verify')->assertOk();

        Mail::assertNothingSent();

        $student = DB::table('recruting_student')
            ->where('email', $payload['email'])
            ->first();

        $this->assertNotNull($student);
        $this->assertSame($payload['email'], $student->email);
        $this->assertSame('registered', $student->status);
        $this->assertSame(0, (int) $student->enabled);
        $this->assertSame(0, (int) $student->blocked);
        $this->assertSame(0, (int) $student->deleted);
        $this->assertSame($payload['name'], $student->name);
        $this->assertSame($payload['name'], $student->lastname);
        $this->assertSame($payload['surname'], $student->surname);
        $this->assertSame($payload['dob'], $student->dob);
        $this->assertSame($payload['language'], $student->language);
        $this->assertSame($payload['parent_name'], $student->parent_name);
        $this->assertSame($payload['parent_surname'], $student->parent_surname);
        $this->assertSame($payload['parent_phone'], $student->parent_phone);
        $this->assertSame($payload['parent_name'], $student->parent1_lastname);
        $this->assertSame($payload['parent_surname'], $student->parent1_surname);
        $this->assertSame($payload['parent_phone'], $student->parent1_phone);
        $this->assertSame($payload['country'], $student->parent1_phone_country);
        $this->assertSame($payload['country'], $student->country);
        $this->assertSame($payload['city'], $student->city);
        $this->assertSame($payload['address'], $student->address);
        $this->assertSame($payload['zip'], $student->zip);
        $this->assertSame($payload['apartment'], $student->apartment);
        $this->assertSame($payload['reg_comment'], $student->reg_comment);
        $this->assertSame(1, (int) $student->photo_consent);
        $this->assertSame(1, (int) $student->terms_accepted);
        $this->assertSame(1, (int) $student->privacy_accepted);
        $this->assertSame(1, (int) $student->data_processing_accepted);
        $this->assertSame(1, (int) $student->urgent_start_accepted);
        $this->assertSame(1, (int) $student->recording_consent_accepted);
        $this->assertSame(1, (int) $student->marketing_consent_accepted);
        $this->assertSame($firstCode, $student->verification_code);
        $this->assertTrue(Hash::check($payload['password'], $student->password));
        $this->assertSame($studentModel->id, $student->id);

        $wrongCode1 = $this->differentCode($firstCode, 1);
        $wrongCode2 = $this->differentCode($firstCode, 2);

        $this->withHeaders($this->verifyHeaders())
            ->postJson('/recruitment/verify-code', [
                'email' => $payload['email'],
                'code' => $wrongCode1,
            ])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertGuest('recruting_student');

        $this->withHeaders($this->verifyHeaders())
            ->postJson('/recruitment/verify-code', [
                'email' => $payload['email'],
                'code' => $wrongCode2,
            ])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertGuest('recruting_student');

        $this->withHeaders($this->verifyHeaders())
            ->postJson('/recruitment/resend-code', [
                'email' => $payload['email'],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        Mail::assertSent(VerificationCodeMailable::class, 1);

        $codes = $this->sentVerificationCodes();
        $secondCode = $codes[0];

        $this->assertNotSame($firstCode, $secondCode);

        $studentAfterResend = DB::table('recruting_student')
            ->where('email', $payload['email'])
            ->first();

        $this->assertSame($secondCode, $studentAfterResend->verification_code);

        $verifyResponse = $this->withHeaders($this->verifyHeaders())
            ->postJson('/recruitment/verify-code', [
                'email' => $payload['email'],
                'code' => $secondCode,
            ]);

        $verifyResponse
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $json = $verifyResponse->json();
        $this->assertIsString($json['api_token'] ?? null);
        $this->assertSame(60, strlen($json['api_token']));

        $verifiedStudent = RecrutingStudent::query()
            ->where('email', $payload['email'])
            ->firstOrFail();

        $this->assertNotNull($verifiedStudent->email_verified_at);
        $this->assertNull($verifiedStudent->verification_code);
        $this->assertSame(1, (int) $verifiedStudent->enabled);
        $this->assertSame($verifiedStudent->id, auth('recruting_student')->id());
        $this->assertAuthenticated('recruting_student');
    }

    public function test_verify_code_and_resend_code_are_rate_limited(): void
    {
        Mail::fake();

        $email = 'student.limit@example.com';
        $this->createPendingStudent([
            'email' => $email,
            'password' => 'secret123',
            'name' => 'Rate',
            'surname' => 'Limited',
            'parent_name' => 'Parent',
            'parent_surname' => 'Limited',
            'parent_phone' => '+48111111111',
            'country' => 'PL',
            'city' => 'Krakow',
            'address' => 'Test 1',
            'zip' => '00-123',
            'apartment' => '1',
            'photo_consent' => 1,
            'terms_accepted' => 1,
            'privacy_accepted' => 1,
            'data_processing' => 1,
            'urgent_start' => 0,
            'recording_consent' => 0,
            'marketing_consent' => 0,
            'reg_comment' => 'limit test',
            'language' => 'pl',
            'dob' => '2014-01-01',
        ], '123456');
        $this->clearStudentThrottle($email);

        // Открываем /verify для получения токена сессии
        $this->get('/verify')->assertOk();

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->withHeaders($this->verifyHeaders())
                ->postJson('/recruitment/verify-code', [
                    'email' => $email,
                    'code' => '654321',
                ])
                ->assertStatus(422)
                ->assertJson(['success' => false]);
        }

        $this->withHeaders($this->verifyHeaders())
            ->postJson('/recruitment/verify-code', [
                'email' => $email,
                'code' => '654321',
            ])
            ->assertStatus(429)
            ->assertJson(['success' => false]);

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->withHeaders($this->verifyHeaders())
                ->postJson('/recruitment/resend-code', [
                    'email' => $email,
                ])
                ->assertOk()
                ->assertJson(['success' => true]);
        }

        $this->withHeaders($this->verifyHeaders())
            ->postJson('/recruitment/resend-code', [
                'email' => $email,
            ])
            ->assertStatus(429)
            ->assertJson(['success' => false]);

        Mail::assertSent(VerificationCodeMailable::class, 3);
        $this->assertGuest('recruting_student');
    }

    /**
     * @return array<int, string>
     */
    private function verifyHeaders(): array
    {
        $baseUrl = rtrim(url('/'), '/');

        return [
            'X-CSRF-TOKEN'        => csrf_token(),
            'X-Form-Token'        => (string) session('verify_form_token'),
            'Origin'              => $baseUrl,
            'Referer'             => $baseUrl . '/verify',
            'X-Requested-With'   => 'XMLHttpRequest',
        ];
    }

    private function sentVerificationCodes(): array
    {
        return Mail::sent(VerificationCodeMailable::class)
            ->map(static fn (VerificationCodeMailable $mail): string => $mail->code)
            ->values()
            ->all();
    }

    private function createPendingStudent(array $payload, string $code): RecrutingStudent
    {
        return RecrutingStudent::query()->create([
            'email' => $payload['email'],
            'password' => Hash::make($payload['password']),
            'status' => 'registered',
            'enabled' => 0,
            'deleted' => 0,
            'blocked' => 0,
            'name' => $payload['name'] ?? '',
            'lastname' => $payload['name'] ?? '',
            'surname' => $payload['surname'] ?? '',
            'dob' => $payload['dob'] ?? null,
            'language' => $payload['language'] ?? 'pl',
            'parent1_surname' => $payload['parent_surname'] ?? '',
            'parent1_lastname' => $payload['parent_name'] ?? '',
            'parent1_phone' => $payload['parent_phone'] ?? '',
            'parent1_phone_country' => $payload['country'] ?? 'PL',
            'parent_name' => $payload['parent_name'] ?? '',
            'parent_surname' => $payload['parent_surname'] ?? '',
            'parent_phone' => $payload['parent_phone'] ?? '',
            'country' => $payload['country'] ?? '',
            'city' => $payload['city'] ?? '',
            'address' => $payload['address'] ?? '',
            'zip' => $payload['zip'] ?? '',
            'apartment' => $payload['apartment'] ?? '',
            'photo_consent' => $payload['photo_consent'] ?? 0,
            'terms_accepted' => $payload['terms_accepted'] ?? 0,
            'privacy_accepted' => $payload['privacy_accepted'] ?? 0,
            'data_processing_accepted' => $payload['data_processing'] ?? 0,
            'urgent_start_accepted' => $payload['urgent_start'] ?? 0,
            'recording_consent_accepted' => $payload['recording_consent'] ?? 0,
            'marketing_consent_accepted' => $payload['marketing_consent'] ?? 0,
            'reg_comment' => $payload['reg_comment'] ?? '',
            'verification_code' => $code,
        ]);
    }

    private function clearStudentThrottle(string $email): void
    {
        $normalizedEmail = strtolower(trim($email));
        $ip = '127.0.0.1';

        RateLimiter::clear('student-verify-code:' . $normalizedEmail . '|' . $ip);
        RateLimiter::clear('student-resend-code:' . $normalizedEmail . '|' . $ip);
    }

    private function differentCode(string $sourceCode, int $offset): string
    {
        $numeric = ((int) $sourceCode + $offset) % 1000000;
        $candidate = str_pad((string) $numeric, 6, '0', STR_PAD_LEFT);

        if ($candidate === $sourceCode) {
            $candidate = str_pad((string) (($numeric + 7) % 1000000), 6, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}

