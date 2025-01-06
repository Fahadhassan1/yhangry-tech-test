<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'set_menu_id',
        'dishes_count',
        'selectable_dishes_count',
        'group_ungrouped',
        'group_mains',
        'group_starter',
        'group_desserts',
    ];

   

    public function setMenu()
    {
        return $this->belongsTo(SetMenu::class);
    }
}
