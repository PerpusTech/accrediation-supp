@extends('layouts.default')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Kunjungan Perhari</h4>
                    <p class="card-title-desc">Halo dan selamat datang di situs kami! Kami senang Anda telah mengunjungi
                        halaman ini. Di sini, Anda akan menemukan berbagai informasi dan layanan yang dapat membantu Anda.
                    </p>
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>NIM</th>
                                <th>Waktu Kunjungan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengunjung as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->surname }}</td>
                                    <td>{{ $item->cardnumber }}</td>
                                    <td>{{ $item->visittime }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
@push('scripts')
    
@endpush
