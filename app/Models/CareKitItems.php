<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CareKitItems extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'care_kit_items';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hospice_care_kit_id','hospice_care_kit_item_id','drug_id','quantity', 'days_supply','sig','createdOn','updatedBy','updatedOn','UpdatedBy'
    ];
    protected $guarded = [];
    protected $hidden = ['_token'];

}
