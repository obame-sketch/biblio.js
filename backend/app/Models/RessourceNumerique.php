<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RessourceNumerique extends Model
{
    protected $fillable = ['titre', 'fichier', 'type_fichier', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}