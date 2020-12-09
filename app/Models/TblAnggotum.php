<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TblAnggotum
 * 
 * @property int $id
 * @property string|null $identitas_anggota
 * @property int|null $id_profil
 * @property Carbon|null $tgl_daftar
 * @property string|null $jenis_anggota
 * @property string|null $aktif
 * @property int|null $user_input
 * @property int|null $user_update
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Profil $profil
 * @property User $user
 *
 * @package App\Models
 */
class TblAnggotum extends Model
{
	use SoftDeletes;
	protected $table = 'tbl_anggota';

	protected $casts = [
		'id_profil' => 'int',
		'user_input' => 'int',
		'user_update' => 'int'
	];

	protected $dates = [
		'tgl_daftar'
	];

	protected $fillable = [
		'identitas_anggota',
		'id_profil',
		'tgl_daftar',
		'jenis_anggota',
		'aktif',
		'user_input',
		'user_update'
	];

	public function profil()
	{
		return $this->belongsTo(Profil::class, 'id_profil');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_update');
	}
}
