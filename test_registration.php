<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Test Registration Script
 * This script tests the registration endpoint and verifies all fields are saved correctly
 */

// Generate a unique test email
$testEmail = 'test_' . time() . '@example.com';

// Test registration payload with all fields
$payload = [
    'email' => $testEmail,
    'password' => 'testpass123',
    'name' => 'Іван',
    'surname' => 'Петренко',
    'lastname' => 'Сергійович',
    'parent_name' => 'Сергій',
    'parent_surname' => 'Петренко',
    'parent_phone' => '+48123456789',
    'parent_passport' => 'AB123456',
    'dob' => '2015-05-15',
    'country' => 'PL',
    'city' => 'Warszawa',
    'address' => 'ul. Marszałkowska 1',
    'zip' => '00-624',
    'apartment' => '10',
    'photo_consent' => true,
    'terms_accepted' => true,
    'privacy_accepted' => true,
    'data_processing' => true,
    'urgent_start' => true,
    'recording_consent' => true,
    'marketing_consent' => false,
    'reg_comment' => 'Test registration for verification',
    'locale' => 'pl',
];

echo "=== Testing Student Registration ===\n\n";
echo "Test Email: {$testEmail}\n\n";

// Send registration request
echo "Sending registration request...\n";
$response = Http::post(url('/api/v1/students/register'), $payload);

if ($response->successful()) {
    echo "✓ Registration request successful\n\n";

    // Verify the student was created
    $student = DB::table('recruting_student')
        ->where('email', $testEmail)
        ->first();

    if (!$student) {
        echo "✗ ERROR: Student not found in database\n";
        exit(1);
    }

    echo "✓ Student found in database (ID: {$student->id})\n\n";

    // Verify all fields
    echo "=== Verifying Fields ===\n\n";

    $expectedFields = [
        'email' => $testEmail,
        'name' => 'Іван',
        'surname' => 'Петренко',
        'lastname' => 'Сергійович',
        'parent_name' => 'Сергій',
        'parent_surname' => 'Петренко',
        'parent_phone' => '+48123456789',
        'parent_passport' => 'AB123456',
        'dob' => '2015-05-15',
        'country' => 'PL',
        'city' => 'Warszawa',
        'address' => 'ul. Marszałkowska 1',
        'zip' => '00-624',
        'apartment' => '10',
        'photo_consent' => 1,
        'terms_accepted' => 1,
        'privacy_accepted' => 1,
        'data_processing_accepted' => 1,
        'urgent_start_accepted' => 1,
        'recording_consent_accepted' => 1,
        'marketing_consent_accepted' => 0,
        'reg_comment' => 'Test registration for verification',
        'status' => 'registered',
    ];

    $errors = 0;
    foreach ($expectedFields as $field => $expectedValue) {
        $actualValue = $student->$field ?? null;

        // Convert boolean to int for comparison
        if (is_bool($expectedValue)) {
            $expectedValue = (int) $expectedValue;
        }

        if ($actualValue != $expectedValue) {
            echo "✗ Field '{$field}': expected '{$expectedValue}', got '{$actualValue}'\n";
            $errors++;
        } else {
            echo "✓ Field '{$field}': {$actualValue}\n";
        }
    }

    echo "\n=== Verification Complete ===\n";

    if ($errors === 0) {
        echo "✓ All fields verified successfully!\n";

        // Clean up test data
        echo "\nCleaning up test data...\n";
        DB::table('recruting_student')->where('email', $testEmail)->delete();
        echo "✓ Test data removed\n";

        exit(0);
    } else {
        echo "✗ {$errors} field(s) failed verification\n";
        exit(1);
    }

} else {
    echo "✗ Registration request failed\n";
    echo "Status: {$response->status()}\n";
    echo "Response: " . $response->body() . "\n";
    exit(1);
}
