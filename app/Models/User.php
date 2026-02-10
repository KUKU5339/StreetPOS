<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'enable_stock_alerts',
        'default_stock_threshold',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'enable_stock_alerts' => 'boolean',
            'default_stock_threshold' => 'integer',
        ];
    }

    // Relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // Helper method
    public function getLowStockProducts()
    {
        return $this->products()
            ->where('stock', '<=', $this->default_stock_threshold)
            ->get();
    }
}
