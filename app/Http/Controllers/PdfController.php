<?php

namespace App\Http\Controllers;

use App\Models\TimeEntries;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function downloadPdf(Request $request, string $id)
    {
        Carbon::setLocale('es');
        try {

            $user = User::findOrFail($id);
            $timeEntries = TimeEntries::where('user_id', $id)
                ->whereMonth('clock_in_at', $request->month)
                ->whereYear('clock_in_at', $request->year)
                ->get();

            $processedEntries = [];

            foreach ($timeEntries as $timeEntry) {
                $start = Carbon::parse($timeEntry->clock_in_at);
                $end = $timeEntry->clock_out_at ? Carbon::parse($timeEntry->clock_out_at) : null;

                $processedEntries[] = [
                    'day' => $start->format('d'),
                    'entry_time' => $start->format('H:i'),
                    'exit_time' => $end ? $end->format('H:i') : '--:--',
                    'hours' => $end ? $end->diff($start)->format('%H:%I') : '00:00',
                ];
            }

            $pdf = Pdf::loadView('PdfTemplate', [
                'entries' => $processedEntries,
                'user' => $user,
                'month' => $request->month,
                'year' => $request->year,
                'currentDate' => Carbon::now(),
            ]);

            return $pdf->download('Registro_menusal_de_jornada.pdf');

        } catch (Exception $e) {
            return response()->json([
                'error' => 'there is a problem getting the pdf',
                'fail' => $e->getMessage(),
            ]);
        }
    }
}
