<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestLog extends Model
{
  protected $table = 'requests';

  protected $fillable = [
      'method', 'slug', 'response'
  ];
}
