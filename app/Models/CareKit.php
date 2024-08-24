<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CareKit extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'carekit';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hospice_care_kit_id','facility_id','name', 'is_active','createdOn','createdBy','updatedOn','UpdatedBy'
    ];
    protected $guarded = [];
    protected $hidden = ['_token'];

}
