<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EmailTemplates extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'email_templates';
    protected $fillable = ['title', 'slug', 'subject', 'body', 'is_active', 'created_at', 'updated_at', 'deleted_at'];

    public function email_cc()
    {
    return $this->hasMany(EmailTemplatesCc::class,'template_id');
    }

}
