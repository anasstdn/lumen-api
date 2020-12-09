<?php
	namespace App\Models;
	use Illuminate\Database\Eloquent\Model;

	class RawData extends Model
	{
		protected $table = 'raw_data';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'tgl_transaksi', 'no_nota','pasir','gendol','abu','split2_3','split1_2','lpa','campur'
    ];

}