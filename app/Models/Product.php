<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['user_id', 'name', 'price', 'stock', 'image'];

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        $supabaseUrl = env('SUPABASE_URL');
        $bucket = env('SUPABASE_STORAGE_BUCKET', 'products');
        $hasSupabase = $supabaseUrl && env('SUPABASE_STORAGE_KEY') && env('SUPABASE_STORAGE_SECRET');

        if ($hasSupabase) {
            return $supabaseUrl . '/storage/v1/object/public/' . $bucket . '/' . $this->image;
        }

        return url('/storage/' . $this->image);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
