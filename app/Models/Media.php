<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $primaryKey = 'media_id';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'file_path',
        'file_type',
        'is_primary',
        'sort_order'
    ];
}
