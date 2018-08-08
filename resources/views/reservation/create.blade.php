@extends('layouts.main')

@section('content')

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <div class="container">
    <div class="row pt-5">
      <div class="col-12 text-center mb-5">
        <h1>Crear una Reservación</h1>
      </div>
      <div class="col-12 text-center">
        @if (count($errors) <> 0)
          @foreach ($errors->toArray() as $message)
            <div class="col-12 form-error">
              {{$message[0]}}
            </div>
          @endforeach
        @endif
        {!! Form::open(['route' => 'reservation.store', 'type' => 'POST', 'class' => 'form-inline','id' => 'reservar']) !!}
        <div class="row col-12 mb-4 text-center">
          <div class="col-3">
          {!! Form::label('first_name','Nombre') !!}
          @if ($errors->has('first_name'))
            {!! Form::label('first_name', $errors->get('first_name')[0], ['class' => 'form-error mb-3']) !!}
            {!! Form::text('first_name',null, ['class' => 'form-control form-error', 'required' => 'required']) !!}
          @else
            {!! Form::text('first_name',null, ['class' => 'form-control', 'required' => 'required']) !!}
          @endif
          </div>
          <div class="col-3">
          {!! Form::label('last_name','Apellidos') !!}
          @if ($errors->has('last_name'))
            {!! Form::label('last_name', $errors->get('last_name')[0], ['class' => 'form-error mb-3']) !!}
            {!! Form::text('last_name',null, ['class' => 'form-control form-error', 'required' => 'required']) !!}
          @else
            {!! Form::text('last_name',null, ['class' => 'form-control', 'required' => 'required']) !!}
          @endif
          </div>
          <div class="col-3">
          {!! Form::label('phone','Teléfono') !!}
          @if ($errors->has('phone'))
            {!! Form::label('phone', $errors->get('phone')[0], ['class' => 'form-error mb-3']) !!}
            {!! Form::text('phone',null, ['class' => 'form-control form-error', 'required' => 'required']) !!}
          @else
            {!! Form::text('phone',null, ['class' => 'form-control', 'required' => 'required']) !!}
          @endif
          </div>
          <div class="col-3">
          {!! Form::label('date','Fecha') !!}
          @if ($errors->has('date'))
            {!! Form::label('date', $errors->get('date')[0], ['class' => 'form-error mb-3']) !!}
            {!! Form::date('date',null, ['class' => 'form-control form-error', 'required' => 'required', 'id' => 'date']) !!}
          @else
            {!! Form::date('date',now(), ['class' => 'form-control', 'required' => 'required', 'id' => 'date']) !!}
          @endif
          </div>
        </div>
        <div class="col-12 mb-4">
          Haga click en los asientos que desea reservar
        </div>
        <div class="container mb-5 ">
          @for ($i=1; $i <= $rows; $i++)
            <div class="row mb-3 text-center">
              <div class="col-12">
                Fila {{$i}}
              </div>
              @for ($j=1; $j <= $columns; $j++)
                {{-- @if (array_search($i.'-'.$j,$takenSeats) === false)
                  <div id="{{$i.'-'.$j}}" class="col text-center rounded border border-primary m-2 seat" title="{{'Fila '.$i.', Columna '.$j }}" onClick="SeatToggle(this.id)">
                    F:{{$i}} C: {{$j}}
                  </div>
                @else
                  <div class="col text-center rounded border border-primary m-2 seat-taken" title="Este puesto ya está reservado" onClick="SeatTaken()">
                    F:{{$i}} C: {{$j}}
                  </div>
                @endif --}}
                <div id="{{$i.'-'.$j}}" class="col text-center rounded border border-primary m-2 seat" title="{{'Fila '.$i.', Columna '.$j }}" onClick="SeatToggle(this.id)">
                  F:{{$i}} C: {{$j}}
                </div>
              @endfor
            </div>
          @endfor
          {!! Form::hidden('seats',null,['id' => 'seats']) !!}
          {!! Form::button('Reservar', ['class' => 'btn btn-primary', 'onClick' => 'SubmitForm();']) !!}
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
  <script type="text/javascript">
  $(document).ready(function(){
    CheckSeats();
  });
  $('#date').on('change', function() {
    CheckSeats();
  });
  var seats = [];
    function SeatToggle(id)
    {
      var elem = $('#'+id);
      if (elem.hasClass('seat-selected')) {
        elem.removeClass('seat-selected');
        seats.splice(seats.indexOf(id), 1);
        $('#seats').val(seats);
      }
      else {
        elem.addClass('seat-selected');
        var content = $('seats').val();
        seats.push(id);
        $('#seats').val(seats);
      }
    }
    $('[name=first_name],[name=last_name]').on('change', function() {
      if (this.value != '') {
        $(this).removeClass('form-error');
      }
    });
    function SubmitForm()
    {
      if (seats != '') {
        if ($('[name=first_name]').val() != '') {
          if ($('[name=last_name]').val() != '') {
            $('form#reservar').submit();
          }
          else {
            alert('Es necesario colocar su apellido para realizar la reservación');
            $('[name=last_name]').addClass('form-error');
          }
        }
        else {
          alert('Es necesario colocar su nombre para realizar la reservación');
          $('[name=first_name]').addClass('form-error');
        }
      }
      else {
        alert('Es necesario seleccionar al menos un asiento para realizar su reservación');
      }
    }
    function SeatTaken()
    {
      alert('Este puesto ya está reservado');
    }

    function CheckSeats()
    {
      $.ajax({
          type: "POST",
          url: '{{route('checkSeats')}}',
          data: {
            date : $("#date").val(),
          },
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            console.log(response);
            for (i = 1; i <= {{$rows}}; i++) {
              for (j = 1; j <= {{$columns}}; j++) {
                  $('#'+i+'-'+j).removeClass('seat-taken');
              }
            }
            $.each(JSON.parse(response), function(index,value) {
              $('#'+value).addClass('seat-taken');
            });
          },
          error: function (response) {

          },
          complete: function () {

          },
      });
    }
  </script>
@endsection
