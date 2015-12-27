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
    
    public function isAdmin() {
	    return $this->level == 4;
    }
    
    public function isManager() {
	    return (int)$this->level > 2;
    }
    
    public function isEditor() {
	    return (int)$this->level > 1;
    }
    
    public function isViewer() {
	    return (int)$this->level > 0;
    }
    
    public function isOwner($entity) {
	    return $this->id == $entity->user_id;
    }
}
