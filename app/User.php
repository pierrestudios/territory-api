<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
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
		'publisher' => 'publisher'
	];
    
    const TYPE_VIEWER = 1;
    const TYPE_EDITOR = 2;
    const TYPE_MANAGER = 3;
    const TYPE_ADMIN = 4;
    
    public static function getType($level) {
	    switch ($level) {
			case 'Viewer':
				return self::TYPE_VIEWER;
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
    
    public static function getTypeString($level) {
	    switch ($level) {
			case self::TYPE_VIEWER:
				return 'Viewer';
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
    public function publisher() {
        return $this->hasOne('App\Publisher');
    }
    
    public function isAdmin() {
	    return $this->level == self::TYPE_ADMIN;
    }
    
    public function isManager() {
	    return (int)$this->level > self::TYPE_EDITOR;
    }
    
    public function isEditor() {
	    return (int)$this->level > self::TYPE_VIEWER;
    }
    
    public function isViewer() {
	    return (int)$this->level > 0;
    }
    
    public function isOwner($entity) {
	    return $this->id == $entity->user_id;
    }
}
