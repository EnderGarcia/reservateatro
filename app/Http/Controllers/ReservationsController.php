<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reservation;
use App\Seat;
use App\User;
use File;


class ReservationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('reservation.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $rows = Seat::max('row');
      $columns = Seat::max('column');
      // $reservations = Reservation::all();
      // $takenSeats = [];
      // foreach ($reservations as $reservation) {
      //   foreach ($reservation->seats()->get() as $seat) {
      //     if ($seat <> null) {
      //       array_push($takenSeats,$seat->row.'-'.$seat->column);
      //     }
      //   }
      // }
      return view('reservation.create')->with([
        'rows' => $rows,
        'columns' => $columns,
      ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
          'first_name' => 'required|max:40|regex:/^[\pL\s]+$/u',
          'last_name' => 'required|max:40|regex:/^[\pL\s]+$/u',
          'date' => 'required|date',
          'phone' => 'required|max:11',
          'seats' => 'required',
        ]);
        if ($request->date.' 00:00:00' < now()->format('Y-m-d').' 00:00:00') {
          return redirect()->back()->withErrors(['evento ya finalizado','El evento que está solicitando ya ha finalizado!'])->withInput($request->input());
        }
        $seats = explode(',',$request->seats);
        $seatResult = [];
        foreach ($seats as $seat) {
          $row = explode('-',$seat)[0];
          $column = explode('-',$seat)[1];
          $foundSeat = Seat::where('row',$row)->where('column',$column)->first();
          array_push($seatResult,$foundSeat->id);
        }
        $reservations = Reservation::where('date','>=',$request->date.' 00:00:00')->where('date','<=',$request->date.' 23:59:59')->get();
        foreach ($reservations as $reservation) {
          $seatsDate = $reservation->seats()->get()->pluck('id')->toArray();
          foreach ($seatResult as $seat) {
            if (array_search($seat,$seatsDate)) {
              return redirect()->back()->withErrors(['reservado','El asiento que ha seleccionado ya ha sido reservado!'])->withInput($request->input());
            }
          }
        }
        $foundUser = User::where('phone',$request->phone)->first();
        if ($foundUser == null) {
          $user = new User;
          $user->first_name = $request->first_name;
          $user->last_name = $request->last_name;
          $user->phone = $request->phone;
          $user->save();
          $userID = $user->id;
        }
        else {
          $userID = $foundUser->id;
        }
        $reservation = new Reservation;
        $reservation->date = $request->date;
        $reservation->user_id = $userID;
        $reservation->save();
        $reservation->seats()->sync($seatResult);
        // $file = File::get(storage_path().'\logs\reservation_log.txt');
        // dd('Hola',$file,storage_path());
        $regLog = "[".now()."] | Registro Realizado | Nombre: [".$request->first_name."] | Apellido: [".$request->last_name."] | Fecha: [".$request->date."]".PHP_EOL;
        File::append(storage_path().'\logs\reservation_log.txt', $regLog);
        return view('reservation.success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function SeatsTaken(Request $request)
    {
      $date = $request->date;
      $reservations = Reservation::where('date','>=',$date.' 00:00:00')->where('date','<=',$date.' 23:59:59')->get();
      $takenSeats = [];
      foreach ($reservations as $reservation) {
        foreach ($reservation->seats()->get() as $seat) {
          if ($seat <> null) {
            array_push($takenSeats,$seat->row.'-'.$seat->column);
          }
        }
      }
      return json_encode($takenSeats);
    }
}
