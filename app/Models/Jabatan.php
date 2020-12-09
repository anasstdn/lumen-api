<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Jabatan
 * 
 * @property int $id
 * @property string|null $jabatan
 * @property string|null $flag_aktif
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class Jabatan extends Model
{
	use SoftDeletes;
	protected $table = 'jabatan';

	protected $fillable = [
		'jabatan',
		'flag_aktif'
	];
}
