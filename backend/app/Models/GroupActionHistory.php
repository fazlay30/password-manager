<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupActionHistory extends Model
{
    use HasFactory;

    protected $table = 'group_project_action_history';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'fk_team_member_id',
        'fk_group_project_cred_id',
        'actions_taken',
    ];

    public function teamMember() {
        return $this->hasOne(TeamMember::class, 'id', 'fk_team_member_id');
    }

    public function groupProjectCredential() {
        return $this->hasOne(GroupProjectCredential::class, 'id', 'fk_group_project_cred_id');
    }
}
