<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livre extends Model
{
    protected $fillable = ['titre', 'isbn', 'description', 'annee_publication', 'auteur_id', 'categorie_id'];

    public function auteur()
    {
        return $this->belongsTo(Auteur::class);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function stock()
    {
        return $this->hasOne(StockLivre::class);
    }

    public function emprunts()
    {
        return $this->hasMany(Emprunt::class);
    }
}