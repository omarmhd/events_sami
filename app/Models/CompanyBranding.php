<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBranding extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'brand_name',
        'logo_url',
        'header_image_url',
        'primary_color',
        'secondary_color',
        'font_family',
        'sender_name',
        'sender_email',
        'reply_to_email',
        'header_html',
        'footer_html',
        'footer_text',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
