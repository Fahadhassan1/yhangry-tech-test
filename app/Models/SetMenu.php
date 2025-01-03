<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'description',
        'image',
        'thumbnail',
        'price_per_person',
        'min_spend',
        'is_vegan',
        'is_vegetarian',
        'is_seated',
        'is_standing',
        'is_canape',
        'is_mixed_dietary',
        'is_meal_prep',
        'is_halal',
        'is_kosher',
        'available',
        'number_of_orders',
        'status',
    ];

    public function cuisines()
    {
        return $this->belongsToMany(Cuisine::class, 'set_menu_cuisines');
    }

    public function menuGroup()
    {
        return $this->hasOne(MenuGroup::class);
    }
}
