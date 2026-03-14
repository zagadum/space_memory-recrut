<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Test Registration Database Script
 * This script directly tests the database insertion to verify all fields are saved correctly
 */

echo "=== Testing Student Registration (Direct DB) ===\n\n";

// Generate a unique test email
$testEmail = 'test_' . time() . '@example.com';
$code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

// Test data with all fields
$dataToInsert = [
    'email'                        => $testEmail,
    'password'                     => Hash::make('testpass123'),
    'status'                       => 'registered',
    'name'                         => 'Іван',
    'surname'                      => 'Петренко',
    'lastname'                     => 'Сергійович',
    'parent_name'                  => 'Сергій',
    'parent_surname'               => 'Петренко',
    'parent_phone'                 => '+48123456789',
    'parent_passport'              => 'AB123456',
    'dob'                          => '2015-05-15',
    'country'                      => 'PL',
    'city'                         => 'Warszawa',
    'address'                      => 'ul. Marszałkowska 1',
    'zip'                          => '00-624',
    'apartment'                    => '10',
    'photo_consent'                => 1,
    'terms_accepted'               => 1,
    'privacy_accepted'             => 1,
    'data_processing_accepted'     => 1,
    'urgent_start_accepted'        => 1,
    'recording_consent_accepted'   => 1,
    'marketing_consent_accepted'   => 0,
    'reg_comment'                  => 'Test registration for verification',
    'verification_code'            => $code,
    'enabled'                      => 0,
    'blocked'                      => 0,
    'deleted'                      => 0,
    'created_at'                   => now(),
    'updated_at'                   => now(),
];

echo "Test Email: {$testEmail}\n";
echo "Verification Code: {$code}\n\n";

// Insert test student
echo "Inserting test student into database...\n";
try {
    $id = DB::table('recruting_student')->insertGetId($dataToInsert);
    echo "✓ Student inserted successfully (ID: {$id})\n\n";
} catch (\Exception $e) {
    echo "✗ ERROR: Failed to insert student\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Retrieve and verify the student
echo "Retrieving student from database...\n";
$student = DB::table('recruting_student')->where('id', $id)->first();

if (!$student) {
    echo "✗ ERROR: Student not found after insertion\n";
    exit(1);
}

echo "✓ Student retrieved successfully\n\n";

// Verify all fields
echo "=== Verifying Fields ===\n\n";

$expectedFields = [
    'email' => $testEmail,
    'status' => 'registered',
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
    'verification_code' => $code,
    'enabled' => 0,
    'blocked' => 0,
    'deleted' => 0,
];

$errors = 0;
$passed = 0;

foreach ($expectedFields as $field => $expectedValue) {
    // Check if field exists
    if (!property_exists($student, $field)) {
        echo "✗ Field '{$field}': MISSING (column does not exist in table)\n";
        $errors++;
        continue;
    }

    $actualValue = $student->$field;

    // Special handling for dates
    if (in_array($field, ['dob'])) {
        $actualValue = $actualValue ? (new DateTime($actualValue))->format('Y-m-d') : null;
    }

    // Convert to same type for comparison
    if (is_bool($expectedValue)) {
        $expectedValue = (int) $expectedValue;
    }
    if (is_bool($actualValue)) {
        $actualValue = (int) $actualValue;
    }

    if ($actualValue != $expectedValue) {
        echo "✗ Field '{$field}': expected '{$expectedValue}', got '{$actualValue}'\n";
        $errors++;
    } else {
        echo "✓ Field '{$field}': {$actualValue}\n";
        $passed++;
    }
}

echo "\n=== Verification Summary ===\n";
echo "Total fields checked: " . count($expectedFields) . "\n";
echo "✓ Passed: {$passed}\n";
echo "✗ Failed: {$errors}\n\n";

// Clean up test data
echo "Cleaning up test data...\n";
DB::table('recruting_student')->where('id', $id)->delete();
echo "✓ Test student removed (ID: {$id})\n\n";

if ($errors === 0) {
    echo "=== ✓ ALL TESTS PASSED ===\n";
    exit(0);
} else {
    echo "=== ✗ TESTS FAILED ===\n";
    exit(1);
}
