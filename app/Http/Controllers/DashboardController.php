<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function umum(){
        $aktif = DB::connection('mysql_server')->table('borrowers as b')
        ->selectRaw('\'Aktif\' AS Status, COUNT(*) AS Jumlah')
        ->where('b.dateexpiry', '>=', DB::raw('CURDATE()'))
        ->where('b.dateenrolled', '<=', DB::raw('CURDATE()'))
        ->first();

        $jmlEksemplar = DB::connection('mysql_server')->table('items as i')
        ->join('biblioitems as bi', 'i.biblionumber', '=', 'bi.biblionumber')
        ->count();

        $jmlBuku = DB::connection('mysql_server')->table('biblio as b')
        ->count();

        $jmlJurnal = DB::connection('mysql_server')->table('items as i')
        ->join('biblioitems as bi', 'i.biblionumber', '=', 'bi.biblionumber')

        ->where('i.itemlost', '=', 0)
        ->whereIn('i.itype', ['JR', 'JRA', 'EJ', 'JRT'])
        ->where('i.homebranch', '=', 'PUSAT')
        ->count();

        return view('dashboard', compact('aktif','jmlEksemplar','jmlBuku','jmlJurnal'));
    }

}
