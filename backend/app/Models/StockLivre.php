<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLivre extends Model
{
    protected $fillable = ['quantite_totale', 'quantite_disponible', 'seuil_alerte', 'livre_id'];

    public function livre()
    {
        return $this->belongsTo(Livre::class);
    }
}