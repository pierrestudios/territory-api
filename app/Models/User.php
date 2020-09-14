<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'level', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes to display is JSON response.
     *
     * @var array
     */
    public static $transformationData = [
        'userId' => 'id',
        'userType' => 'level',
        'email' => 'email',
        'date' => 'created_at',
        'publisher' => 'publisher'
    ];

    const TYPE_VIEWER = 1;
    const TYPE_NOTE_EDITOR = 5;
    const TYPE_EDITOR = 2;
    const TYPE_MANAGER = 3;
    const TYPE_ADMIN = 4;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function getType($level)
    {
        switch ($level) {
        case 'Viewer':
            return self::TYPE_VIEWER;
        case 'NoteEditor':
            return self::TYPE_NOTE_EDITOR;
        case 'Editor':
            return self::TYPE_EDITOR;
        case 'Manager':
            return self::TYPE_MANAGER;
        case 'Admin':
            return self::TYPE_ADMIN;
        default:
            return 1;
        }
    }

    public static function getTypeString($level)
    {
        switch ($level) {
        case self::TYPE_VIEWER:
            return 'Viewer';
        case self::TYPE_NOTE_EDITOR:
            return 'NoteEditor';
        case self::TYPE_EDITOR:
            return 'Editor';
        case self::TYPE_MANAGER:
            return 'Manager';
        case self::TYPE_ADMIN:
            return 'Admin';
        default:
            return 'Viewer';
        }
    }

    /**
     * Get the publisher associated with the user.
     */
    public function publisher()
    {
        return $this->hasOne('App\Models\Publisher');
    }

    public function isAdmin()
    {
        return $this->level == self::TYPE_ADMIN && !($this->level == self::TYPE_NOTE_EDITOR);
    }

    public function isManager()
    {
        return (int)$this->level > self::TYPE_EDITOR && !($this->level == self::TYPE_NOTE_EDITOR);
    }

    public function isEditor()
    {
        return (int)$this->level > self::TYPE_VIEWER && !($this->level == self::TYPE_NOTE_EDITOR);
    }

    public function isNoteEditor()
    {
        return (int)$this->level > self::TYPE_VIEWER;
    }

    public function isViewer()
    {
        return (int)$this->level > 0;
    }

    public function isOwner($entity)
    {
        return $this->id == $entity->user_id;
    }
}
