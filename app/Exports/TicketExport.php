<?php

namespace App\Exports;

use App\Models\EventInvitation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TicketExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * The company ID to scope the export (multi-tenant isolation).
     */
    private ?int $companyId;

    public function __construct(?int $companyId = null)
    {
        $this->companyId = $companyId;
    }

    /**
     * Return only the authenticated company's invitations.
     */
    public function collection()
    {
        return EventInvitation::with('event:id,name,title,date')
            ->when($this->companyId, fn ($q) => $q->where('company_id', $this->companyId))
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'اسم المدعو',
            'البريد الإلكتروني',
            'المسمى الوظيفي',
            'الجنسية',
            'الفعالية',
            'عدد المرافقين',
            'الحالة',
            'تاريخ الإرسال',
            'تاريخ الرد',
        ];
    }

    public function map($invitation): array
    {
        return [
            $invitation->id,
            $invitation->invitee_name,
            $invitation->invitee_email,
            $invitation->invitee_position,
            $invitation->invitee_nationality,
            $invitation->event?->name ?? $invitation->event?->title ?? '—',
            $invitation->selected_guests ?? 0,
            $this->mapStatus($invitation->status),
            $invitation->created_at?->format('Y-m-d H:i') ?? '—',
            $invitation->responded_at?->format('Y-m-d H:i') ?? '—',
        ];
    }

    /**
     * Translate status values to Arabic for readability.
     */
    private function mapStatus(?string $status): string
    {
        return match ($status) {
            'accepted' => 'مقبول',
            'declined' => 'مرفوض',
            'pending'  => 'في الانتظار',
            'maybe'    => 'ربما',
            default    => $status ?? '—',
        };
    }

    public function styles(Worksheet $sheet): array
    {
        // Auto-fit column widths for better readability
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [
            // Header row: bold white text on teal background, centered
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size'  => 11,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0F8F83'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
