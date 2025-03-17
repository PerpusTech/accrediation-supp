@extends('layouts.default')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Page Not Found</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Accreditation Support</a></li>
                        <li class="breadcrumb-item active">Page Not Found</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-l-8">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <h1 class="text-center font-semibold text-2xl">Oops! Page Not Found</h1>
                        <div class="alert alert-danger text-center" role="alert">
                            <p><i class="mdi mdi-block-helper me-2 "></i>The page you are looking for might have
                                been removed
                                or temporarily unavailable.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <a href="{{ url('/') }}" class="btn btn-primary">Go Back to Home</a>
@endsection
