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
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property bool|null $status_aktif
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|DataLog[] $data_logs
 * @property Collection|Notification[] $notifications
 * @property Collection|Profil[] $profils
 * @property Collection|TblAnggotum[] $tbl_anggota
 * @property Collection|TblSetting[] $tbl_settings
 *
 * @package App\Models
 */
class User extends Model
{
	use SoftDeletes;
	protected $table = 'users';

	protected $casts = [
		'status_aktif' => 'bool'
	];

	protected $dates = [
		'email_verified_at'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'name',
		'username',
		'email',
		'email_verified_at',
		'password',
		'status_aktif',
		'remember_token'
	];

	public function data_logs()
	{
		return $this->hasMany(DataLog::class);
	}

	public function notifications()
	{
		return $this->hasMany(Notification::class, 'user_sender_id');
	}

	public function profils()
	{
		return $this->hasMany(Profil::class, 'user_update');
	}

	public function tbl_anggota()
	{
		return $this->hasMany(TblAnggotum::class, 'user_update');
	}

	public function tbl_settings()
	{
		return $this->hasMany(TblSetting::class, 'user_input');
	}
}
