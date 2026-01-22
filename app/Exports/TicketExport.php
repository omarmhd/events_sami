<?php

namespace App\Exports;

use App\Models\Ticket;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketExport implements FromCollection, WithHeadings, WithMapping,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return EventInvitation::get();
    }
    public function headings(): array
    {
        $table->foreignId('event_id')->nullable()->constrained("events")->cascadeOnDelete();
        $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
        $table->string('invitee_name');
        $table->string('invitee_email')->nullable();
        $table->string('invitee_phone')->nullable();
        $table->string('invitee_position')->nullable();
        $table->string('invitee_nationality')->nullable();
        $table->uuid('invitation_token')->unique();
        $table->string('status', 20)->default('pending')->index();
        $table->unsignedTinyInteger('allowed_guests')->default(1);
        $table->unsignedTinyInteger('selected_guests')->default(0);
        $table->timestamp('responded_at')->nullable();
        return [
            'Invitation #',
            'Invitee Name',
            'Invitee Email',
            'Invitee Position',
            'Invitee Nationality',
            'Guests',
            "Status",
            'Sent Date',
            'Responded Date',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->id,
            $ticket->invitee_email, // Assuming you have a customer relationship
            $ticket->invitee_position,
            $ticket->invitee_nationality,
            $ticket->selected_guests,
            $ticket->status,
            $ticket->created_at,
            $ticket->responded_at,
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            // Apply styles to the first row (header row)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFF'], // White text color
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => '0000FF'], // Blue background color
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

}
