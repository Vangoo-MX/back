<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotsFavorites extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'list_favorites_lots';
}
