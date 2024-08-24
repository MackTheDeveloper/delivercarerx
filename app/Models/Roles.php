<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Roles extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'roles';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['parent_id', 'role_title', 'role_slug', 'role_type', 'is_active'];

    /**
     * permissions() many-to-many relationship method
     *
     * @return QueryBuilder
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'permission_role','role_id');
        //return $this->hasMany(PermissionRole::class,'role_id','id');
    }
}
