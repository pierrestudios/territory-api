<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Street extends Model
{
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
