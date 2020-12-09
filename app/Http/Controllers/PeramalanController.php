<?php

namespace App\Http\Controllers;
use App\Models\RawData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DatePeriod;
use DateTime;
use DateInterval;
date_default_timezone_set("Asia/Jakarta");

class PeramalanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        // $this->middleware('auth',['only' => [
        //     'index',
        //     'create',
        //     'update',
        //     'destroy',
        //     'show'
        // ]]);
    }

    public function index(Request $request)
    {
        $input = $request->all();
        $periode            =   array();
        $aktual             =   array();
        $peramalan_arrses   =   array();
        $peramalan_des      =   array();

        $mad_arrses         =   0;
        $pe_arrses          =   0;

        $mad_des            =   0;
        $pe_des             =   0;


        $arrses             =   $this->forecastingArrses($input['tanggal_awal'],$input['tanggal_akhir'],$input['produk']);
        $des                =   $this->forecastingDes($input['tanggal_awal'],$input['tanggal_akhir'],$input['produk']);


        if((isset($arrses) && !empty($arrses)) && (isset($des) && !empty($des)))
        {
            $length_arrses  =   count($arrses) - 1;
            $length_des     =   count($des) - 1;

            foreach($arrses as $key => $val)
            {
                array_push($periode,$val['periode']);
                array_push($aktual,$val['aktual']);
                array_push($peramalan_arrses,$val['peramalan']);

                $mad_arrses +=  $val['MAD'];
                $pe_arrses  +=  $val['percentage_error'];
            }

            foreach ($des as $key => $val) {
                array_push($peramalan_des,$val['peramalan']);

                $mad_des    +=  $val['MAD'];
                $pe_des     +=  $val['PE'];
            }

            $data = array(
                'arrses' => $arrses,
                'des' => $des,
                'periode' => $periode,
                'aktual' => $aktual,
                'peramalan_arrses' => $peramalan_arrses,
                'peramalan_des' => $peramalan_des,
                'length_arrses' => $length_arrses,
                'length_des' => $length_des,
                'mad_arrses' => $mad_arrses,
                'pe_arrses' => $pe_arrses,
                'mad_des' => $mad_des,
                'pe_des' => $pe_des,
                'tanggal_awal' => $input['tanggal_awal'],
                'tanggal_akhir' => $input['tanggal_akhir'],
                'produk' => $input['produk'],
                'status' => true
            );
        }
        else
        {
            $data = array(
                'status' => false,
                'msg' => 'Tidak ditemukan transaksi penjualan antara periode '.$input['$tanggal_awal'].' sampai '.$input['$tanggal_akhir']
            );
        }

        return response()->json($data);
    }

    public function forecastingArrses($tanggal_awal,$tanggal_akhir,$nama_produk)
    {
        $date_from  =   date('Y-m-d',strtotime($tanggal_awal));
        $date_to    =   date('Y-m-d',strtotime($tanggal_akhir));  

        $data_penjualan     =   RawData::select(DB::raw('DATE_FORMAT(tgl_transaksi, "%v/%x") as minggu,IF(sum(pasir) IS NULL,0,sum(pasir)) as pasir,IF(sum(gendol) IS NULL, 0, sum(gendol)) as gendol,IF(sum(abu) IS NULL,0,sum(abu)) as abu, IF(sum(split2_3) IS NULL,0,sum(split2_3)) as split2_3, IF(sum(split1_2) IS NULL, 0, sum(split2_3)) as split1_2, IF(sum(lpa) IS NULL,0,sum(lpa)) as lpa'))
        ->where('tgl_transaksi','>=',$date_from)
        ->where('tgl_transaksi','<=',$date_to)
        // ->groupby('tgl_transaksi')
        ->groupBy(DB::raw('DATE_FORMAT(tgl_transaksi, "%v/%x")'))
        ->orderby('tgl_transaksi','ASC')
        ->get();

        if(isset($data_penjualan) && !$data_penjualan->isEmpty()){
            // $minggu=$this->week_between_two_dates($date_from,$date_to);
            $minggu = array();
            $total = array();
            $subtotal = 0;
            
            foreach($data_penjualan as $key => $val)
            {
                array_push($minggu,$val->minggu);
                switch($nama_produk)
                {
                    case 'abu':
                    $subtotal = floatval($val->abu);
                    break;
                    case 'gendol':
                    $subtotal = floatval($val->gendol);
                    break;
                    case 'pasir':
                    $subtotal = floatval($val->pasir);
                    break;
                    case 'split2_3':
                    $subtotal = floatval($val->split2_3);
                    break;
                    case 'split1_2':
                    $subtotal = floatval($val->split1_2);
                    break;
                    case 'lpa':
                    $subtotal = floatval($val->lpa);
                    break;
                }
                array_push($total,$subtotal);
            }

            $result=$this->arrses($data_penjualan,$minggu,$total,$date_to);
        }
        else
        {
            $result=array();
        }
        return $result;
    }

    private function arrses($data_penjualan,$periode,$total,$date_to)
    {
        $periode=$periode;
        $X=$total;
        $F = array();
        $e = array();
        $E = array();
        $AE = array();
        $alpha = array();
        $beta = [0.01,0.02,0.03,0.04,0.05,0.06,0.07,0.08,0.09,0.10,0.11,0.12,0.13,0.14,0.15,0.16,0.17,0.18,0.19,0.20, 0.21,0.22,0.23,0.24,0.25,0.26,0.27,0.28,0.29,0.30,0.31,0.32,0.33,0.34,0.35,0.36,0.37,0.38,0.39,0.40,0.41,0.42,0.43,0.44,0.45,0.46,0.47,0.48,0.49,0.50,0.51,0.52,0.53,0.54,0.55,0.56,0.57,0.58,0.59,0.60,0.61,0.62,0.63,0.64,0.65,0.66,0.67,0.68,0.69,0.70,0.71,0.72,0.73,0.74,0.75,0.76,0.77,0.78,0.79,0.80,0.81,0.82,0.83,0.84,0.85,0.86,0.87,0.88,0.89,0.90,0.91,0.92,0.93,0.94,0.95,0.96,0.97,0.98,0.99];

        // $beta=[0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9];
        $PE = array();
        $MAPE = array();
        $MAD=array();

        for($i = 0; $i < count($beta); $i++) 
        {
            $F[$i][0] = $e[$i][0] = $E[$i][0] = $AE[$i][0] = $alpha[$i][0] = $PE[$i][0] =$MAD[$i][0]= 0;
            $F[$i][1] = $X[0];
            $alpha[$i][1] = $beta[$i];

            for($j = 1; $j < count($periode); $j++){
                // perhitungan peramalan untuk periode berikutnya
                $F[$i][$j + 1] = ($alpha[$i][$j] * $X[$j]) + ((1 - $alpha[$i][$j]) * $F[$i][$j]);

                // menghitung selisih antara nilai aktual dengan hasil peramalan
                $e[$i][$j] = $X[$j] - $F[$i][$j]; 

                // menghitung nilai kesalahan peramalan yang dihaluskan
                $E[$i][$j] = ($beta[$i] * $e[$i][$j]) + ((1 - $beta[$i]) * $E[$i][$j - 1]);

                // menghitung nilai kesalahan absolut peramalan yang dihaluskan
                $AE[$i][$j] = ($beta[$i] * abs($e[$i][$j])) + ((1 - $beta[$i]) * $AE[$i][$j - 1]);

                // menghitung nilai alpha untuk periode berikutnya
                $alpha[$i][$j + 1] = $E[$i][$j] == 0 ? $beta[$i] : abs($E[$i][$j] / $AE[$i][$j]);

                // menghitung nilai kesalahan persentase peramalan
                $PE[$i][$j] = $X[$j] == 0 ? 0 : abs((($X[$j] - $F[$i][$j]) / $X[$j]) * 100);
                
                $MAD[$i][$j] = $X[$j] == 0 ? 0 : abs(($X[$j] - $F[$i][$j]));
            }

            // menghitung rata-rata kesalahan peramalan
            // $MAPE[$i] = array_sum($PE[$i])/(count($periode) - 1);
            $MAPE[$i] = array_sum($PE[$i])/(count($periode));
        }
        // dd($MAD);
        $bestBetaIndex = array_search(min($MAPE), $MAPE);

        $hasil = array();
        for ($i = 0; $i <= count($periode); $i++) {
            if ($i < count($periode)) {
                $hasil[$i] = [
                    'periode'                   => $periode[$i],
                    'aktual'                    => $X[$i],
                    'peramalan'                 => $F[$bestBetaIndex][$i],
                    'galat'                     => $e[$bestBetaIndex][$i],
                    'galat_pemulusan'           => $E[$bestBetaIndex][$i],
                    'galat_pemulusan_absolut'   => $AE[$bestBetaIndex][$i],
                    'alpha'                     => $alpha[$bestBetaIndex][$i],
                    'percentage_error'          => $PE[$bestBetaIndex][$i],
                    'MAD'                       => $MAD[$bestBetaIndex][$i],

                ];
            } else {
                // $nextPeriode = date('W', strtotime(date($date_to)));
                $nextPeriode = Carbon::parse($date_to)->addWeeks(1)->format('W/Y');
                $hasil[$i] = [
                    'periode'                   => $nextPeriode,
                    'aktual'                    => 0,
                    'peramalan'                 => $F[$bestBetaIndex][$i],
                    'galat'                     => 0,
                    'galat_pemulusan'           => 0,
                    'galat_pemulusan_absolut'   => 0,
                    'alpha'                     => $alpha[$bestBetaIndex][$i],
                    'percentage_error'          => 0,
                    'MAD'                       => 0,
                ];
            }
        }

        return $hasil;
    }

    public function forecastingDes($tanggal_awal,$tanggal_akhir,$nama_produk)
    {
        $date_from      =   date('Y-m-d',strtotime($tanggal_awal));
        $date_to        =   date('Y-m-d',strtotime($tanggal_akhir));  

        $data_penjualan     =   RawData::select(DB::raw('DATE_FORMAT(tgl_transaksi, "%v/%x") as minggu,IF(sum(pasir) IS NULL,0,sum(pasir)) as pasir,IF(sum(gendol) IS NULL, 0, sum(gendol)) as gendol,IF(sum(abu) IS NULL,0,sum(abu)) as abu, IF(sum(split2_3) IS NULL,0,sum(split2_3)) as split2_3, IF(sum(split1_2) IS NULL, 0, sum(split2_3)) as split1_2, IF(sum(lpa) IS NULL,0,sum(lpa)) as lpa'))
            ->where('tgl_transaksi','>=',$date_from)
            ->where('tgl_transaksi','<=',$date_to)
        // ->groupby('tgl_transaksi')
            ->groupBy(DB::raw('DATE_FORMAT(tgl_transaksi, "%v/%x")'))
            ->orderby('tgl_transaksi','ASC')
            ->get();

            if(isset($data_penjualan) && !$data_penjualan->isEmpty()){
            // $minggu=$this->week_between_two_dates($date_from,$date_to);
        // dd($minggu);
            $minggu = array();
            $total = array();
            $subtotal = 0;
            
            foreach($data_penjualan as $key => $val)
            {
                array_push($minggu,$val->minggu);
                switch($nama_produk)
                {
                    case 'abu':
                    $subtotal = floatval($val->abu);
                    break;
                    case 'gendol':
                    $subtotal = floatval($val->gendol);
                    break;
                    case 'pasir':
                    $subtotal = floatval($val->pasir);
                    break;
                    case 'split2_3':
                    $subtotal = floatval($val->split2_3);
                    break;
                    case 'split1_2':
                    $subtotal = floatval($val->split1_2);
                    break;
                    case 'lpa':
                    $subtotal = floatval($val->lpa);
                    break;
                }
                array_push($total,$subtotal);
            }

        // $periode=$this->getPeriode($date_from,$date_to);
            // $total=$this->getTotal($minggu,$data_penjualan,$produk   =   $nama_produk);
        // dd($total);
            $result=$this->des1($data_penjualan,$minggu,$total,$date_to);
        }
        else
        {
            $result=array();
        }
        // dd($result);
        return $result;
    }

    private function des1($data_penjualan,$periode,$total,$date_to)
    {
        $periode=$periode;
        $X=$total;
        $F = array();
        $s1 = array();
        $s2 = array();
        $at = array();
        $bt = array();
        $alpha=[0.01,0.02,0.03,0.04,0.05,0.06,0.07,0.08,0.09,0.10,0.11,0.12,0.13,0.14,0.15,0.16,0.17,0.18,0.19,0.20, 0.21,0.22,0.23,0.24,0.25,0.26,0.27,0.28,0.29,0.30,0.31,0.32,0.33,0.34,0.35,0.36,0.37,0.38,0.39,0.40,0.41,0.42,0.43,0.44,0.45,0.46,0.47,0.48,0.49,0.50,0.51,0.52,0.53,0.54,0.55,0.56,0.57,0.58,0.59,0.60,0.61,0.62,0.63,0.64,0.65,0.66,0.67,0.68,0.69,0.70,0.71,0.72,0.73,0.74,0.75,0.76,0.77,0.78,0.79,0.80,0.81,0.82,0.83,0.84,0.85,0.86,0.87,0.88,0.89,0.90,0.91,0.92,0.93,0.94,0.95,0.96,0.97,0.98,0.99];
        // $alpha=[0.01];
        $PE = array();
        $MAPE = array();
        $MAD=array();

        for($i=0;$i<count($alpha);$i++)
        {
            $F[$i][0]=$bt[$i][0]=$MAD[$i][0]=$PE[$i][0]=0;
            $s1[$i][0]=$s2[$i][0]=$X[0];
            $at[$i][0]=(2*$s1[$i][0])-$s2[$i][0];

            for($j=0;$j<count($periode);$j++)
            {
                // $s1[$i][$j+1]=($alpha[$i] * $X[$j+1]) + ((1-$alpha[$i]) * $s1[$i][$j]);
                if($j!==count($periode)-1)
                {                   
                    $s1[$i][$j+1]=($alpha[$i] * $X[$j+1]) + ((1-$alpha[$i]) * $s1[$i][$j]);
                    $s2[$i][$j+1]=($alpha[$i] * $s1[$i][$j+1]) + ((1-$alpha[$i]) * $s2[$i][$j]);

                    $at[$i][$j+1]=(2*$s1[$i][$j+1])-$s2[$i][$j+1];
                    $bt[$i][$j+1]=($alpha[$i]/(1-$alpha[$i]))*($s1[$i][$j+1]-$s2[$i][$j+1]);
                    $F[$i][$j+1]=$at[$i][$j+1]+$bt[$i][$j+1];

                    $MAD[$i][$j+1]=$X[$j+1]==0?0:abs($X[$j+1]-$F[$i][$j+1]);
                    $PE[$i][$j+1]=$X[$j+1] == 0 ? 0 : abs((($X[$j+1] - $F[$i][$j+1]) / $X[$j+1]) * 100);
                }
                else
                {
                    $s1[$i][$j+1]=0;
                    $s2[$i][$j+1]=0;
                    $at[$i][$j+1]=0;
                    $bt[$i][$j+1]=0;
                    $F[$i][$j+1]=$at[$i][$j]+($bt[$i][$j] * 1);
                    $MAD[$i][$j+1]=0;
                    $PE[$i][$j+1]=0;
                }
            }
            $MAPE[$i] = array_sum($PE[$i])/(count($periode)+1);
        }

        $bestAlphaIndex = array_search(min($MAPE), $MAPE);

        $hasil = array();
        for ($i = 0; $i <= count($periode); $i++) {
            if($i<count($periode))
            {
                $hasil[$i] = [
                    'periode'                   => $periode[$i],
                    'aktual'                    => $X[$i],
                    'peramalan'                 => $F[$bestAlphaIndex][$i],
                    'alpha'                     => $alpha[$bestAlphaIndex],
                    's1'                        => $s1[$bestAlphaIndex][$i],
                    's2'                        => $s2[$bestAlphaIndex][$i],
                    'at'                        => $at[$bestAlphaIndex][$i],
                    'bt'                        => $bt[$bestAlphaIndex][$i],
                    'MAD'                       => $MAD[$bestAlphaIndex][$i],
                    'PE'                        => $PE[$bestAlphaIndex][$i],
                ];
            }
            else
            {
                $nextPeriode = Carbon::parse($date_to)->addWeeks(1)->format('W/Y');
                $hasil[$i] = [
                    'periode'                   => $nextPeriode,
                    'aktual'                    => 0,
                    'peramalan'                 => $F[$bestAlphaIndex][$i],
                    'alpha'                     => $alpha[$bestAlphaIndex],
                    's1'                        => $s1[$bestAlphaIndex][$i],
                    's2'                        => $s2[$bestAlphaIndex][$i],
                    'at'                        => $at[$bestAlphaIndex][$i],
                    'bt'                        => $bt[$bestAlphaIndex][$i],
                    'MAD'                       => $MAD[$bestAlphaIndex][$i],
                    'PE'                        => $PE[$bestAlphaIndex][$i],
                ];
            }
        }
        // echo '<pre>';
        // print_r($hasil);
        // echo '</pre>';
        // die;
        return $hasil;
    }
}