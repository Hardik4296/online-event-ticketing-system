<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EventsExport implements FromCollection, WithHeadings, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$events = Event::with('city')
	        ->get();

        $eventNumber = 1;
	    return $events->map(function ($event) use (&$eventNumber) {
	        return [
	            'id' => $eventNumber++,
	            'title' => $event->title,
	            'description' => $event->description,
	            'event_date_time' => $event->event_date_time,
	            'event_duration' => $event->event_duration,
	            'location' => $event->location,
	            'city' => $event->city->name ?? 'N/A',
	            'status' => $event->status,
	            'created_at' => $event->created_at,
	        ];
	    });
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return [
        	"No",
        	"Title",
        	"Description",
        	"Event Date Time",
        	"Event Duration",
        	"Location",
        	"City",
        	"Status",
        	"Created At"
        ];
    }

    /**
     * Apply styles to the spreadsheet.
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']]],
            'A1:I1' => ['fill' => ['fillType' => 'solid', 'color' => ['argb' => '580b9b']]],
        ];
    }
}
