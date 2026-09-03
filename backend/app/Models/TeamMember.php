<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $table = 'team_members';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'fk_group_project_id',
        'description',
        'fk_role_id',
        'email',
        'invitation_token',
        'fk_user_id',
        'status',
    ];

    public function groupProject() {
        return $this->belongsTo(GroupProject::class, 'fk_group_project_id', 'id');
    }

    public function user() {
        return $this->hasOne(User::class, 'id', 'fk_user_id');
    }

    public function role() {
        return $this->hasOne(Role::class, 'id', 'fk_role_id');
    }
}
