<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $baseSlug = Str::slug($category->name);
                $category->slug = static::where('slug', $baseSlug)->exists()
                    ? $baseSlug . '-' . Str::random(4)
                    : $baseSlug;
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}