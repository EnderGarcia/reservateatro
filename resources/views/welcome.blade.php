@extends('layouts.main')

@section('content')
  <div class="container">
    <div class="row pt-5">
      <div class="col-12 text-center mb-5">
        <h1>Reserva Teatro</h1>
      </div>
      <div class="col-12 mb-5">
        <div class="row col-3 mx-auto">
          <a href="{{route('reservation.create')}}">Realizar una Reservación</a>
        </div>
      </div>
    </div>
  </div>
@endsection
