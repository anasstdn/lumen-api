<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserProfil
 * 
 * @property int $id
 * @property string $user_id
 * @property int|null $profil_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Profil $profil
 *
 * @package App\Models
 */
class UserProfil extends Model
{
	protected $table = 'user_profil';

	protected $casts = [
		'profil_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'profil_id'
	];

	public function profil()
	{
		return $this->belongsTo(Profil::class);
	}
}
