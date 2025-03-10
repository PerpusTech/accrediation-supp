<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function umum(){
        $aktif = Cache::remember('aktif_data', 60, function () {
            return DB::connection('mysql_server')->table('borrowers as b')
                ->selectRaw('\'Aktif\' AS Status, COUNT(*) AS Jumlah')
                ->where('b.dateexpiry', '>=', DB::raw('CURDATE()'))
                ->where('b.dateenrolled', '<=', DB::raw('CURDATE()'))
                ->first();
        });

        // Data Eksemplar
        $jmlEksemplar = Cache::remember('jml_eksemplar', 60, function () {
            return DB::connection('mysql_server')->table('items as i')
                ->join('biblioitems as bi', 'i.biblionumber', '=', 'bi.biblionumber')
                ->count();
        });

        // Data Buku
        $jmlBuku = Cache::remember('jml_buku', 60, function () {
            return DB::connection('mysql_server')->table('biblio as b')
                ->count();
        });

        // Data Jurnal
        $jmlJurnal = Cache::remember('jml_jurnal', 60, function () {
            return DB::connection('mysql_server')->table('items as i')
                ->join('biblioitems as bi', 'i.biblionumber', '=', 'bi.biblionumber')
                ->where('i.itemlost', '=', 0)
                ->whereIn('i.itype', ['JR', 'JRA', 'EJ', 'JRT'])
                ->where('i.homebranch', '=', 'PUSAT')
                ->count();
        });

        // Data Kunjungan (Bulan)
        $currentYear = Carbon::now()->year;
        $currentMonth = 12;

        // Ambil data kunjungan per bulan
        $kunjunganData = DB::connection('mysql_server')->table('visitorhistory')
            ->selectRaw('MONTH(visittime) as bulan, COUNT(*) as jumlah')
            ->whereYear('visittime', $currentYear)
            ->whereMonth('visittime', '>=', 1) // Ambil data dari Januari
            ->whereMonth('visittime', '<=', $currentMonth) // Sampai bulan saat ini
            ->groupBy('bulan')
            ->get();  // Ambil semua data dalam satu query

        // Buat data bulan dan jumlah kunjungan
        $dataBulan = [];
        $dataTotalBulan = [];
        for ($i = 1; $i <= $currentMonth; $i++) {
            // Jika bulan ada di data, ambil jumlahnya, jika tidak, set 0
            $kunjungan = $kunjunganData->firstWhere('bulan', $i);
            $dataBulan[] = Carbon::create()->month($i)->format('F');
            $dataTotalBulan[] = $kunjungan ? $kunjungan->jumlah : 0;
        }

        // DATA SIRKULASI BUKU
        $currentYear = date('Y');
        $currentMonth = date('m');

        // Ambil data peminjaman berdasarkan tipe
        $issueData = DB::connection('mysql_server')->table('statistics as c')
            ->selectRaw('MONTH(c.datetime) as bulan, COUNT(*) as jumlah')
            ->where('c.type', 'issue')
            ->whereYear('c.datetime', $currentYear)
            ->whereMonth('c.datetime', '>=', 1) // Ambil data dari Januari
            ->whereMonth('c.datetime', '<=', $currentMonth) // Sampai bulan saat ini
            ->groupBy('bulan')
            ->get();

        $renewData = DB::connection('mysql_server')->table('statistics as c')
            ->selectRaw('MONTH(c.datetime) as bulan, COUNT(*) as jumlah')
            ->where('c.type', 'renew')
            ->whereYear('c.datetime', $currentYear)
            ->whereMonth('c.datetime', '>=', 1)
            ->whereMonth('c.datetime', '<=', $currentMonth)
            ->groupBy('bulan')
            ->get();

        $returnData = DB::connection('mysql_server')->table('statistics as c')
            ->selectRaw('MONTH(c.datetime) as bulan, COUNT(*) as jumlah')
            ->where('c.type', 'return')
            ->whereYear('c.datetime', $currentYear)
            ->whereMonth('c.datetime', '>=', 1)
            ->whereMonth('c.datetime', '<=', $currentMonth)
            ->groupBy('bulan')
            ->get();

        // Buat data bulan dan jumlah untuk masing-masing tipe
        $dataPerBulan = [];
        $dataIssue = [];
        $dataRenew = [];
        $dataReturn = [];

        for ($i = 1; $i <= $currentMonth; $i++) {
            $dataPerBulan[] = Carbon::create()->month($i)->format('F');

            // Ambil jumlah peminjaman berdasarkan bulan untuk masing-masing tipe
            $issue = $issueData->firstWhere('bulan', $i);
            $dataIssue[] = $issue ? $issue->jumlah : 0;

            $renew = $renewData->firstWhere('bulan', $i);
            $dataRenew[] = $renew ? $renew->jumlah : 0;

            $return = $returnData->firstWhere('bulan', $i);
            $dataReturn[] = $return ? $return->jumlah : 0;
        }

        // Kirim data ke view
        return view('dashboard', compact(
            'aktif', 'jmlEksemplar', 'jmlBuku', 'jmlJurnal',
            'dataBulan', 'dataTotalBulan', 'dataIssue', 'dataRenew', 'dataReturn', 'dataPerBulan'
        ));
    }
}

