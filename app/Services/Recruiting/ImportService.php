<?php

declare(strict_types=1);

namespace App\Services\Recruiting;

use App\Models\RecruitingCampaign;
use App\Models\RecruitingStudentImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

final class ImportService
{
    /**
     * Parse CSV/Excel, create campaign, insert rows.
     * Returns campaign with total_count.
     */
    public function importFromFile(UploadedFile $file, string $campaignName, string $emailSubject, string $emailTemplate, ?string $createdBy = null): RecruitingCampaign
    {
        $campaign = RecruitingCampaign::create([
            'name'           => $campaignName,
            'email_subject'  => $emailSubject,
            'email_template' => $emailTemplate,
            'created_by'     => $createdBy,
            'status'         => 'draft',
        ]);

        $rows = $this->parseFile($file);
        $count = 0;

        foreach ($rows as $row) {
            $email = trim($row['email'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            // Skip duplicates within same campaign
            $exists = RecruitingStudentImport::query()
                ->where('campaign_id', $campaign->id)
                ->where('email', $email)
                ->exists();

            if ($exists) {
                continue;
            }

            // Skip if already in recruting_student
            $alreadyStudent = DB::table('recruting_student')
                ->where('email', $email)
                ->exists();

            if ($alreadyStudent) {
                continue;
            }

            RecruitingStudentImport::create([
                'email'       => $email,
                'name'        => trim($row['name'] ?? $row['imie'] ?? ''),
                'surname'     => trim($row['surname'] ?? $row['nazwisko'] ?? ''),
                'phone'       => trim($row['phone'] ?? $row['telefon'] ?? ''),
                'subject'     => trim($row['subject'] ?? $row['przedmiot'] ?? ''),
                'source'      => 'csv_import',
                'campaign_id' => $campaign->id,
                'token'       => Str::random(48),
                'status'      => 'pending',
            ]);

            $count++;
        }

        $campaign->update(['total_count' => $count]);

        return $campaign;
    }

    private function parseFile(UploadedFile $file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());

        if ($ext === 'csv') {
            return $this->parseCsv($file);
        }

        // xlsx via PhpSpreadsheet or Maatwebsite/Excel
        // For now, CSV only
        throw new \InvalidArgumentException("Unsupported file type: {$ext}. Use CSV.");
    }

    private function parseCsv(UploadedFile $file): array
    {
        $rows = [];
        $handle = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle, 0, ';'); // Polish Excel often uses ;
        if (!$headers) {
            $headers = fgetcsv($handle, 0, ',');
        }

        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

        while (($line = fgetcsv($handle, 0, ';')) !== false) {
            if (count($line) !== count($headers)) {
                continue;
            }
            $rows[] = array_combine($headers, $line);
        }

        fclose($handle);
        return $rows;
    }
}
