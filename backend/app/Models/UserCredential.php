<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCredential extends Model
{
    use HasFactory;

    protected $table = 'user_credentials';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'site_name',
        'site_url',
        'username',
        'password',
        'fk_user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'fk_user_id', 'id');
    }
}
