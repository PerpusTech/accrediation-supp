<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function umum(){
        // ========================DATA ANGGOTA AKTIF================================
        $aktif = Cache::remember('aktif_data', 60, function () {
            return DB::connection('mysql_server')->table('borrowers as b')
                ->selectRaw('\'Aktif\' AS Status, COUNT(*) AS Jumlah')
                ->where('b.dateexpiry', '>=', DB::raw('CURDATE()'))
                ->where('b.dateenrolled', '<=', DB::raw('CURDATE()'))
                ->first();
        });

        // ========================DATA JUDUL DAN EKSEMPLAR================================
        // Ambil data dari cache atau query jika belum ada
        $result = Cache::remember('items_summary', 60, function () {
        return DB::connection('mysql_server')->table('items as i')
            ->select(
                DB::raw("CASE WHEN LEFT(bi.cn_class, 1) IS NULL THEN 'Total' ELSE CONCAT(LEFT(bi.cn_class, 1), 'XX') END AS Kelas"),
                DB::raw("COUNT(DISTINCT i.biblionumber) AS Judul"),
                DB::raw("COUNT(i.itemnumber) AS Eksemplar")
            )
            ->leftJoin('biblioitems as bi', 'bi.biblioitemnumber', '=', 'i.biblioitemnumber')
            ->leftJoin('biblio as b', 'b.biblionumber', '=', 'i.biblionumber')
            ->where('i.damaged', 0)
            ->where('i.itemlost', 0)
            ->where('i.withdrawn', 0)
            ->whereRaw('LEFT(i.itype, 2) = ?', ['BK'])
            ->where('i.homebranch', 'PUSAT')
            ->groupBy(DB::raw('LEFT(bi.cn_class, 1)'))
            ->get();
    });

        // Pisahkan data untuk 'Judul' dan 'Eksemplar'
        $judulData = $result->map(function ($item) {
            return [
                'Kelas' => $item->Kelas,
                'Judul' => $item->Judul,
            ];
        });
        $eksemplarData = $result->map(function ($item) {
            return [
                'Kelas' => $item->Kelas,
                'Eksemplar' => $item->Eksemplar,
            ];
        });
        // Menghitung jumlah total
        $totalJudul = $result->sum('Judul');
        $totalEksemplar = $result->sum('Eksemplar');

        // ====================================DATA JURNAL====================================
        $cacheKey = 'biblioitems_data'; // You can change this key to something more descriptive if necessary
        // Use Cache::remember to cache the results for a given period (e.g., 60 minutes)
        $results = Cache::remember($cacheKey, 60, function () {
        return DB::connection('mysql_server')->table('items as i')
            ->join('biblioitems as bi', 'i.biblionumber', '=', 'bi.biblionumber')
            ->join('biblio as b', 'i.biblionumber', '=', 'b.biblionumber')
            ->join('itemtypes as i1', 'i.itype', '=', 'i1.itemtype')
            ->join('biblio_metadata as bm', 'b.biblionumber', '=', 'bm.biblionumber')
            ->select(
                'bi.cn_class as Kelas',
                DB::raw("CONCAT(EXTRACTVALUE(bm.metadata, '//datafield[@tag=\"245\"]/subfield[@code=\"a\"]'), ' ', EXTRACTVALUE(bm.metadata, '//datafield[@tag=\"245\"]/subfield[@code=\"b\"]')) as Judul"),
                'bi.publishercode as Penerbit',
                DB::raw('COUNT(DISTINCT i.itemnumber) as Issue'),
                DB::raw('SUM(i.copynumber) as Eksemplar'),
                'i1.description as Jenis',
                DB::raw("EXTRACTVALUE(bm.metadata, '//datafield[@tag=\"260\"]/subfield[@code=\"c\"]') as Tahun_Terbit")
            )
            ->where('i.itemlost', 0)
            ->whereIn('i.itype', ['JR', 'JRA', 'EJ', 'JRT'])
            ->where('i.homebranch', 'PUSAT')
            ->groupBy('Judul', 'Penerbit', 'Tahun_Terbit')
            ->orderBy('Judul')
            ->get();
        });

        // Now count the total number of results
        $totalJurnal = $results->count();



        // ====================================DATA KUNJUNGAN====================================
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

        // =========================DATA SIRKULASI BUKU================================
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

        // =======================DATA BUKU PALING LARIS================================
        $startDate = Carbon::now()->subYear()->format('Ym');  // Format 'YYYYMM'
        $endDate = Carbon::now()->format('Ym');  // Format 'YYYYMM'

        // Tentukan waktu cache, misalnya 60 menit
        $cacheKey = 'top_books_' . $startDate . '_' . $endDate;
        $cacheDuration = 60; // Menyimpan cache selama 60 menit

        // Cek cache terlebih dahulu
        $result = Cache::remember($cacheKey, $cacheDuration, function () use ($startDate, $endDate) {
            return DB::connection('mysql_server')->table('statistics')
                ->select(
                    'b1.title',
                    'b1.author',
                    'i.itemcallnumber',
                    'i.itemnumber',
                    'i.barcode',
                    DB::raw('count(b1.title) as xPinjamanInPeriode'),
                    'i.issues as xPinjamanTotal'
                )
                ->leftJoin('items as i', 'statistics.itemnumber', '=', 'i.itemnumber')
                ->leftJoin('biblio as b1', 'b1.biblionumber', '=', 'i.biblionumber')
                ->leftJoin('biblioitems as b2', 'b2.biblioitemnumber', '=', 'i.biblioitemnumber')
                ->where('statistics.type', '=', 'issue')
                ->whereBetween(DB::raw('EXTRACT(YEAR_MONTH FROM statistics.datetime)'), [$startDate, $endDate])
                ->groupBy('statistics.itemnumber')
                ->orderBy(DB::raw('COUNT(b1.title)'), 'desc')
                ->limit(10)
                ->get();
        });

        // ==========================KUNJUNGAN PERHARI PER FAKULTAS===========================
        $sekarang = date('Y-m-d');  // Tanggal sekarang

        // Cache key yang unik untuk menyimpan hasil query
        $cacheKey = 'visitor_history_' . $sekarang;

        // Menggunakan Cache::remember untuk menyimpan query ke dalam cache
        $query = Cache::remember($cacheKey, 60, function () use ($sekarang) {
        // Menyusun query SQL dan melakukan parameter binding menggunakan DB::select
        $sql = "
            SELECT 'FKIP' AS fakultas, COUNT(*) AS count
            FROM visitorhistory
            WHERE LEFT(visitorhistory.cardnumber, 1) = 'A'
            AND LENGTH(visitorhistory.cardnumber) = 10
            AND DATE(visitorhistory.visittime) = ?
            UNION
            SELECT 'EKONOMI', COUNT(*)
            FROM visitorhistory
            WHERE LEFT(visitorhistory.cardnumber, 1) = 'B'
            AND LENGTH(visitorhistory.cardnumber) = 10
            AND DATE(visitorhistory.visittime) = ?
            UNION
            SELECT 'HUKUM', COUNT(*)
            FROM visitorhistory
            WHERE LEFT(visitorhistory.cardnumber, 1) = 'C'
            AND LENGTH(visitorhistory.cardnumber) = 10
            AND DATE(visitorhistory.visittime) = ?
            UNION
            SELECT 'TEKNIK', COUNT(*)
            FROM visitorhistory
            WHERE LEFT(visitorhistory.cardnumber, 1) = 'D'
            AND LENGTH(visitorhistory.cardnumber) = 10
            AND DATE(visitorhistory.visittime) = ?
            UNION
            SELECT 'GEOGRAFI', COUNT(*)
            FROM visitorhistory
            WHERE LEFT(visitorhistory.cardnumber, 1) = 'E'
            AND LENGTH(visitorhistory.cardnumber) = 10
            AND DATE(visitorhistory.visittime) = ?
            UNION
            SELECT 'PSIKOLOGI', COUNT(*)
            FROM visitorhistory
            WHERE LEFT(visitorhistory.cardnumber, 1) = 'F'
            AND LENGTH(visitorhistory.cardnumber) = 10
            AND DATE(visitorhistory.visittime) = ?
            UNION
            SELECT 'FAI', COUNT(*)
            FROM visitorhistory
            WHERE LEFT(visitorhistory.cardnumber, 1) = 'G'
            AND LENGTH(visitorhistory.cardnumber) = 10
            AND DATE(visitorhistory.visittime) = ?
            UNION
            SELECT 'OTHER', COUNT(*)
            FROM visitorhistory
            WHERE LENGTH(visitorhistory.cardnumber) != 10
            AND DATE(visitorhistory.visittime) = ?
        ";

        // Menjalankan query SQL dengan parameter binding
        return  DB::connection('mysql_server')->select($sql, [$sekarang, $sekarang, $sekarang, $sekarang, $sekarang, $sekarang, $sekarang, $sekarang]);
        });

        // dd($query);

        // Menyusun data untuk chart
        $labels = [];
        $counts = [];
        foreach ($query as $data) {
            $labels[] = $data->fakultas;
            $counts[] = (int)$data->count;
        }

        // Kirim data ke view
        return view('dashboard', compact(
            'aktif', 'totalEksemplar', 'totalJudul', 'totalJurnal',
            'dataBulan', 'dataTotalBulan', 'dataIssue', 'dataRenew', 'dataReturn', 'dataPerBulan', 'result', 'labels', 'counts'
        ));
    }
}

