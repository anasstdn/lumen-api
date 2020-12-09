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
 * Class Profil
 * 
 * @property int $id
 * @property string|null $nama_depan
 * @property string|null $nama_belakang
 * @property string|null $nik
 * @property string|null $jenis_kelamin
 * @property string|null $agama
 * @property string|null $status_perkawinan
 * @property string|null $alamat_domisili
 * @property string|null $kota_domisili
 * @property string|null $alamat_ktp
 * @property string|null $kota_ktp
 * @property string|null $tempat_lahir
 * @property Carbon|null $tgl_lahir
 * @property string|null $no_telp
 * @property string|null $email
 * @property string|null $foto
 * @property int|null $user_input
 * @property int|null $user_update
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 * @property Collection|TblAnggotum[] $tbl_anggota
 * @property Collection|UserProfil[] $user_profils
 *
 * @package App\Models
 */
class Profil extends Model
{
	use SoftDeletes;
	protected $table = 'profil';

	protected $casts = [
		'user_input' => 'int',
		'user_update' => 'int'
	];

	protected $dates = [
		'tgl_lahir'
	];

	protected $fillable = [
		'nama_depan',
		'nama_belakang',
		'nik',
		'jenis_kelamin',
		'agama',
		'status_perkawinan',
		'alamat_domisili',
		'kota_domisili',
		'alamat_ktp',
		'kota_ktp',
		'tempat_lahir',
		'tgl_lahir',
		'no_telp',
		'email',
		'foto',
		'user_input',
		'user_update'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_update');
	}

	public function tbl_anggota()
	{
		return $this->hasMany(TblAnggotum::class, 'id_profil');
	}

	public function user_profils()
	{
		return $this->hasMany(UserProfil::class);
	}
}
