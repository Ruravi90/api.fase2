@extends('layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Calendario
    <small></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> inicio</a></li>
    <li class="active">Calendario</li>
  </ol>
</section>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Calendario</div>

                <div class="panel-body">
                    <div id="fullcalendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
  <!-- fullcalendar -->
  <script src="{{ asset('js/plugins/fullcalendar.js') }}"></script>
  <script src="{{ asset('js/controllers/ScheduleController.js') }}"></script>
@endsection