<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Street extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_apt_building', 'street'
    ];

    /**
     * The attributes to display is JSON response.
     *
     * @var array
     */
    public static $transformationData = [
        'streetId' => 'id',
        'isAptBuilding' => 'is_apt_building',
        'street' => 'street'
    ];

    public static $intKeys = [
        'isAptBuilding'
    ];
}
