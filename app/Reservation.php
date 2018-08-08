<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
  protected $fillable = [
      'date', 'seats',
  ];
  public function user()
  {
    return $this->belongsTo('App\User');
  }
  public function seats()
  {
    return $this->belongsToMany('App\Seat');
  }
}
