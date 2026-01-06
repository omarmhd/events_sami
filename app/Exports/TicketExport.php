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
        return Ticket::get();
    }
    public function headings(): array
    {
        return [
            'Ticket #',
            'job no',
            'event name',
            'employee name',
            'employee email',
            "Type",
            'checked in at',
            'register time',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->id,
            $ticket->employee_number, // Assuming you have a customer relationship
            $ticket->event_name,
            $ticket->employee_name,
            $ticket->employee_email,
            $ticket->is_children=="yes"?"child":"employee",
            $ticket->checked_in_at,
            $ticket->created_at,
            // Add other columns as needed
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
