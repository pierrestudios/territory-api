<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    /**
     * The status for "Unverified".
     *
     * @var int
     */
    public const STATUS_UNVERIFIED = 0;

    /**
     * The status for "Valid".
     *
     * @var int
     */
    public const STATUS_VALID = 1;

    /**
     * The status for "Not Current Language".
     *
     * @var int
     */
    public const STATUS_NOT_CURRENT_LANGUAGE = 2;

    /**
     * The status for "Not In Service".
     *
     * @var int
     */
    public const STATUS_NOT_IN_SERVICE = 3;

    /**
     * The status for "Do Not Call".
     *
     * @var int
     */
    public const STATUS_DO_NOT_CALL = 4;

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
        'number' => 'number',
        'status' => 'status',
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

    /**
     *  Get the validated status for the phone.
     */
    public static function getValidatedStatus($status = 0) {
        return array_search($status, [
            static::STATUS_UNVERIFIED,
            static::STATUS_VALID,
            static::STATUS_NOT_CURRENT_LANGUAGE,
            static::STATUS_NOT_IN_SERVICE,
            static::STATUS_DO_NOT_CALL
        ]) == false ? 0 : (int)$status;
    }
}
