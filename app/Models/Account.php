<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;

class Account extends Model
{
  use HasFactory;

  protected $table = "accounts";

  public $fillable = [
    'balance'
  ];

  public function payment()
  {
    return $this->hasMany(Payment::class);
  }
}
