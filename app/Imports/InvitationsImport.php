<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

/**
 * InvitationsImport
 * ─────────────────────────────────────────────────────────────────────────────
 * Parses an uploaded Excel (.xlsx) or CSV file into a collection of invitation
 * row arrays. The controller is responsible for creating invitations and
 * dispatching email jobs from these rows.
 *
 * Expected columns (header row):
 *   name, email, position, nationality, allowed_guests
 *
 * Column names are normalised (trimmed, lower-cased) before matching so the
 * template can be edited by users without breaking the import.
 */
class InvitationsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /** @var array Parsed rows after import */
    private array $rows = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $name  = trim((string) ($row['name'] ?? $row['الاسم'] ?? ''));
            $email = trim((string) ($row['email'] ?? $row['البريد_الالكتروني'] ?? $row['البريد الإلكتروني'] ?? ''));

            // Skip rows with no name or invalid email.
            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $this->rows[] = [
                'invitee_name'        => $name,
                'invitee_email'       => $email,
                'invitee_position'    => trim((string) ($row['position'] ?? $row['المسمى_الوظيفي'] ?? $row['المسمى الوظيفي'] ?? '')),
                'invitee_nationality' => trim((string) ($row['nationality'] ?? $row['الجنسية'] ?? '')),
                'allowed_guests'      => (int) ($row['allowed_guests'] ?? $row['المرافقون'] ?? 0),
            ];
        }
    }

    /**
     * Returns the parsed rows ready for use by the controller.
     */
    public function getRows(): array
    {
        return $this->rows;
    }
}
