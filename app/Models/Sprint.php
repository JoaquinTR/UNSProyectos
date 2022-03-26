<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    use HasFactory;

    protected $table = 'sprint';
    protected $primaryKey = 'id';

    protected $fillable = [
        'numero','nota_final','iniciado','entregado','deadline'
    ];
}
