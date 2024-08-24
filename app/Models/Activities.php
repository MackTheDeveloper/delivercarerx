<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activities extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'activities';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'module_name',
        'description',
        'performed_by',
        'is_active'
    ];

    /**
     * relationship to get user information
     *
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'performed_by');
    }
}
