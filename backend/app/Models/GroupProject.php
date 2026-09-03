<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupProject extends Model
{
    use HasFactory;

    protected $table = 'group_projects';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'description',
        'fk_user_id',
    ];

    public function groupOwner() {
        return $this->belongsTo(User::class, 'fk_user_id', 'id');
    }
}
