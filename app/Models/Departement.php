<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Departement
 * 
 * @property int $id
 * @property string|null $departement
 * @property string|null $flag_aktif
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class Departement extends Model
{
	use SoftDeletes;
	protected $table = 'departement';

	protected $fillable = [
		'departement',
		'flag_aktif'
	];
}
