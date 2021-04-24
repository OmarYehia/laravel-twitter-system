<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class PdfController extends Controller
{
    public function pdfDownload(Request $request)
    {
        // pass data
        // all users + total number of tweets they made
        //
        $pdf = PDF::loadView('report');
        return $pdf->download('user_report.pdf');
    }
}
