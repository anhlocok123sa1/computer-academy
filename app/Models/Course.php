<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['category_id', 'title', 'slug', 'thumbnail', 'description', 'price', 'level', 'is_published'];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function sections() {
        return $this->hasMany(Section::class)->orderBy('sort_order');
    }

    public function enrollments() {
        return $this->hasMany(Enrollment::class);
    }
}
