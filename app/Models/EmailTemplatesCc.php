<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplatesCc extends Model
{
    use HasFactory;
    protected $table = 'email_templates_cc';

    protected $fillable = ['template_id', 'email_cc', 'created_at', 'updated_at'];

    // public function emailTemplate(){
    //     return $this->belongsToMany(EmailTemplates::class,'email_templates');
    // }
}
