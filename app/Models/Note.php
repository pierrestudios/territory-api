<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'entity_id', 'entity', 'date', 'content', 'archived'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public static $transformationData = [
        'noteId' => 'id',
        'userId' => 'user_id',
        'date' => 'date',
        'note' => 'content',
        'retain' => 'archived'
    ];

    public static $intKeys = [
        'noteId'
    ];

    /**
     * Get the address for the note.
     */
    public function address()
    {
        return $this->belongsTo('App\Address', 'entity_id', 'id');
    }
}
