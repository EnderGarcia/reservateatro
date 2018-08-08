@extends('layouts.main')

@section('content')
  <div class="container">
    <div class="row pt-5">
      <div class="col-12 text-center">
        <h1>Reservaciones</h1>
      </div>
      <div class="col-12">
        <div class="row">
          <a href="{{route('reservation.create')}}">Realizar una Reservación</a>
        </div>
      </div>
    </div>
  </div>
@endsection
