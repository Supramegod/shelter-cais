@extends('layouts.master')
@section('title','Dashboard Aktifitas Sales')
@section('pageStyle')
    <!-- PivotTable CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pivottable/2.23.0/pivot.min.css">
    <!-- C3 Chart CSS (Optional for charts) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.css">
    <!-- jQuery UI CSS (Required for drag and drop functionality) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-primary"
                    ><i class="mdi mdi-finance mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">5 </h4>
                </div>
                <p class="mb-0 text-heading ">Aktifitas Sales Hari Ini</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-warning">
                    <i class="mdi mdi-finance mdi-20px"></i>
                    </span>
                </div>
                <h4 class="ms-1 mb-0 display-6">2</h4>
                </div>
                <p class="mb-0 text-heading ">Aktifitas Sales Minggu Ini</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-secondary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-secondary">
                    <i class="mdi mdi-finance mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">3</h4>
                </div>
                <p class="mb-0 text-heading ">Aktifitas Sales Bulan Ini</p>
            </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-info h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-info"
                    ><i class="mdi mdi-finance mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">4</h4>
                </div>
                <p class="mb-0 text-heading ">Aktifitas Sales Tahun Ini</p>
            </div>
            </div>
        </div>
    </div>
    <div class="row gy-4 mb-5">
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-primary"
                    ><i class="mdi mdi-finance mdi-20px"></i
                    ></span>
                </div>
                <h4 class="ms-1 mb-0 display-6">5 </h4>
                </div>
                <p class="mb-0 text-heading ">Aktifitas Sales Hari Ini</p>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageScript')
    
    </script>
@endsection

