<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
  protected $fillable = [
      'row', 'column',
  ];
  public function reservation()
  {
    return $this->belongsToMany('App\Reservation');
  }
}
