<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterHeading extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'code_category_id', 'image']; 

    public function codeCategory()
    {
        return $this->belongsTo(CodeCategory::class);
    }
}
