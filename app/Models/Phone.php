<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address_id', 'status', 'name', 'number'
    ];

    /**
     * The attributes to display is JSON response.
     *
     * @var array
     */
    public static $transformationData = [
        'phoneId' => 'id',
        'addressId' => 'address_id',
        'name' => 'name',
        'status' => 'status',
        'number' => 'number',
        'notes' => 'notes'
    ];

    public static $intKeys = [
        'phoneId',
        'addressId',
        'status'
    ];

    /**
     * Get the address for the phone.
     */
    public function address()
    {
        return $this->belongsTo('App\Models\Address', 'address_id', 'id');
    }

    /**
     * Get the notes for the phone.
     */
    public function notes()
    {
        return $this->hasMany('App\Models\Note', 'entity_id', 'id');
    }
}
