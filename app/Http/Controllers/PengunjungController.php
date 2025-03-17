<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class PengunjungController extends Controller
{
    public function umum(){
        $sekarang = Carbon::today()->toDateString();
        // Query using Laravel query builder
        $pengunjung = DB::connection('mysql_server')->table('visitorhistory')
            ->join('borrowers', 'visitorhistory.cardnumber', '=', 'borrowers.cardnumber')
            ->whereDate('visittime', $sekarang)  // whereDate compares only the date part
            ->orderBy('visittime', 'desc')
            ->get();
            return view('pages.pengunjung', compact('pengunjung'));
        }

    


}
