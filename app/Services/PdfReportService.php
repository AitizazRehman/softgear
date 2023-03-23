<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use PDF;

class PdfReportService
{
    public function exportIncomeReport($request)
    {
        $header = [
            'Content-Type' => 'application/*',
        ];
        view()->share('incomeData', $request);
        $pdf = PDF::loadView('exports.export-income-report', $request);
        // $path = Storage::put('public/attendancereport/attendance-report.pdf', $pdf->output());
        $path = 'income-report.pdf';
        $pdf->save($path);
        $file_data['path'] = $path;
        $file_data['header'] =  $header;
        return $file_data;
    }  
    public function exportExpenseReport($request)
    {
        $header = [
            'Content-Type' => 'application/*',
        ];
        view()->share('expenseData', $request);
        $pdf = PDF::loadView('exports.export-expense-report', $request);
        $path = 'export-report.pdf';
        $pdf->save($path);
        $file_data['path'] = $path;
        $file_data['header'] =  $header;
        return $file_data;
    }
}
