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
 * Class Agama
 * 
 * @property int $id
 * @property string|null $nama_agama
 * @property string|null $flag_aktif
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Profil[] $profils
 *
 * @package App\Models
 */
class Agama extends Model
{
	use SoftDeletes;
	protected $table = 'agama';

	protected $fillable = [
		'nama_agama',
		'flag_aktif'
	];

	public function profils()
	{
		return $this->hasMany(Profil::class, 'id_agama');
	}
}
