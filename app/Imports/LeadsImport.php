<?php

namespace App\Imports;

use App\Models\{ImportLog, Lead, LeadSource, Service, User};
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadsImport implements ToCollection, WithHeadingRow
{
    public function __construct(private User $user) {}
    public function collection(Collection $rows)
    {
        $imported = 0; $skipped = 0; $errors = [];
        foreach ($rows as $row) {
            $mobile = trim((string) ($row['mobile_number'] ?? $row['mobile'] ?? ''));
            if (!$mobile || Lead::where('mobile_number', $mobile)->exists()) { $skipped++; continue; }
            try { Lead::create(['user_id' => $this->user->id, 'full_name' => $row['full_name'] ?? $row['name'] ?? 'Unnamed lead', 'mobile_number' => $mobile, 'email' => $row['email'] ?? null, 'company_name' => $row['company_name'] ?? null, 'city' => $row['city'] ?? null, 'state' => $row['state'] ?? null, 'lead_source_id' => LeadSource::firstOrCreate(['name' => $row['lead_source'] ?? 'Other'])->id, 'service_id' => Service::firstOrCreate(['name' => $row['interested_service'] ?? 'Custom Software'])->id, 'remarks' => $row['remarks'] ?? null]); $imported++; } catch (\Throwable $exception) { $errors[] = $exception->getMessage(); $skipped++; }
        }
        ImportLog::create(['user_id' => $this->user->id, 'filename' => 'spreadsheet import', 'total_rows' => $rows->count(), 'imported_rows' => $imported, 'skipped_rows' => $skipped, 'errors' => $errors]);
    }
}
