<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sprint;

class Comision extends Model
{
    use HasFactory;

    protected $table = 'comision';
    protected $primaryKey = 'id';

    protected $fillable = [
        //rellenar
    ];

    /**
     * Obtener las imágenes que fueron cargadas a este juego.
     */
    public function sprints()
    {
        return $this->hasMany('App\Models\Sprint');
    }
}
