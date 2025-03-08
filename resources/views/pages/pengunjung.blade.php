@extends('layouts.default')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title">Buttons Example</h4>
                    <p class="card-title-desc">The Buttons extension for DataTables
                        provides a common set of options, API methods and styling to display
                        buttons on a page that will interact with a DataTable. The core library
                        provides the based framework upon which plug-ins can built.
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
