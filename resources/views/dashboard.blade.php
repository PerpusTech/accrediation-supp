@extends('layouts.default')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Dashboard</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Accreditation Support</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Jurnal</p>
                            <h4 class="mb-2">{{ $totalJurnal }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="ri-book-open-fill font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div><!-- end cardbody -->
            </div><!-- end card -->
        </div><!-- end col -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Judul Buku</p>
                            <h4 class="mb-2">{{ $totalJudul }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="ri-book-2-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div><!-- end cardbody -->
            </div><!-- end card -->
        </div><!-- end col -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Eksemplar</p>
                            <h4 class="mb-2">{{ $totalEksemplar }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="ri-booklet-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div><!-- end cardbody -->
            </div><!-- end card -->
        </div><!-- end col -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Anggota Aktif</p>
                            <h4 class="mb-2">{{ $aktif->Jumlah }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="ri-user-3-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div><!-- end cardbody -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->

    <div class="row">
        <div class="col-xl-6">

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Grafik Data Kunjungan Tahun {{ date('Y') }}</h4>
                </div>
                <div class="card-body py-0 px-2">
                    <div id="chart" class="p-3" dir="ltr"></div>
                </div>
            </div><!-- end card -->
        </div>
        <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Grafik Data Sirkulasi Tahun {{ date('Y') }}</h4>
                </div>
                <div class="card-body py-0 px-2">
                    <div id="columnChart" class="p-3" dir="ltr"></div>
                </div>
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                    </div>

                    <h4 class="card-title mb-4">Buku Terlaris Dipinjam di Tahun {{ date('Y') }}</h4>
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul Buku</th>
                                    <th>Penulis</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead><!-- end thead -->
                            <tbody>
                                @foreach ($result as $book)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ Str::limit($book->title, 30) }}</td>
                                        <td>{{ $book->author }}</td>
                                        <td>{{ $book->xPinjamanInPeriode }}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                            <!-- end tbody -->
                        </table> <!-- end table -->
                    </div>
                </div><!-- end card -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Kunjungan Harian Fakultas</h4>

                    <div class="mt-4">
                        <div id="chartfak" class="apex-charts" dir="ltr">
                        </div>
                    </div>
                </div><!-- end card -->
            </div><!-- end col -->
        </div>
        <!-- end row -->
    @endsection
    @push('scripts')
        <script>
            var options = {
                series: [{
                    name: "Total Kunjungan",
                    data: @json($dataTotalBulan)
                }],
                chart: {
                    height: 350,
                    type: 'area',
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'stepline'
                },
                // title: {
                //     text: 'Grafik Data Kunjungan {{ date('Y') }}',
                //     align: 'center'
                // },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: @json($dataBulan)
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
        </script>
        <script>
            var options2 = {
                series: [{
                        name: 'Peminjaman Buku',
                        data: @json($dataIssue)
                    },
                    {
                        name: 'Perpanjangan Buku ',
                        data: @json($dataRenew)
                    },
                    {
                        name: 'Pengembalian Buku',
                        data: @json($dataReturn)
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 5,
                        borderRadiusApplication: 'end'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: @json($dataPerBulan),
                },
                // title: {
                //     text: 'Data Sirkulasi Perbulan {{ date('Y') }}',
                //     align: 'center',
                // },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val
                        }
                    }
                }
            };

            var chart2 = new ApexCharts(document.querySelector("#columnChart"), options2);
            chart2.render();
        </script>
        <script type="text/javascript">
            var labels = @json($labels); // Data fakultas
            var counts = @json($counts); // Data count

            var options = {
                series: counts, // Jumlah pengunjung
                chart: {
                    width: 380,
                    type: 'pie',
                },
                labels: labels, // Nama fakultas
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart = new ApexCharts(document.querySelector("#chartfak"), options);
            chart.render();
        </script>
    @endpush
