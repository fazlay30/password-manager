<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupProjectCredential extends Model
{
    use HasFactory;

    protected $table = 'group_project_credentials';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'site_name',
        'site_url',
        'username',
        'password',
        'fk_group_project_id',
    ];

    public function groupProject() {
        return $this->hasOne(GroupProject::class, 'id', 'fk_group_project_id');
    }
}
