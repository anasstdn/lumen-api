<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class StatusPerkawinan
 * 
 * @property int $id
 * @property string|null $status_perkawinan
 * @property string|null $flag_aktif
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Profil[] $profils
 *
 * @package App\Models
 */
class StatusPerkawinan extends Model
{
	use SoftDeletes;
	protected $table = 'status_perkawinan';

	protected $fillable = [
		'status_perkawinan',
		'flag_aktif'
	];

	public function profils()
	{
		return $this->hasMany(Profil::class, 'status_perkawinan');
	}
}
