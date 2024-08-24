<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Permission extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'permissions';

    protected $fillable = ['id', 'permission_title', 'permission_slug', 'permission_route', 'permission_group', 'permission_description', 'is_active'];

    /**
     * roles() many-to-many relationship method
     *
     * @return QueryBuilder
     */
    public function roles()
    {
        return $this->belongsToMany(Roles::class);
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }

    public static function getIdBySlug($slug)
    {
        return self::where('permission_slug', $slug)->first();
    }
}
