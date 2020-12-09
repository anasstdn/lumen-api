<?php

namespace App\Http\Controllers;
use App\Models\RawData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
date_default_timezone_set("Asia/Jakarta");

class TransaksiController extends Controller
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

    //
    //
    public function index(Request $request)
    {
      $input = $request->all();

      $data_penjualan  = RawData::select(DB::raw('DATE_FORMAT(tgl_transaksi, "%v/%x") as minggu, COUNT(*) as total'))
      ->whereYear('tgl_transaksi','=',$input['tahun'])
      ->groupBy(DB::raw('DATE_FORMAT(tgl_transaksi, "%v/%x")'))
      ->orderby('tgl_transaksi','ASC')
      ->get();

      $minggu = array();
      $total_transaksi=array();

      if(isset($data_penjualan) && !$data_penjualan->isEmpty())
      {
        foreach($data_penjualan as $key => $val)
        {
          array_push($minggu,$val->minggu);
          array_push($total_transaksi,$val->total);
        }
      }

      $penjualan_barang   = RawData::select(DB::raw('DATE_FORMAT(tgl_transaksi, "%v/%x") as minggu,IF(sum(pasir) IS NULL,0,sum(pasir)) as pasir,IF(sum(gendol) IS NULL, 0, sum(gendol)) as gendol,IF(sum(abu) IS NULL,0,sum(abu)) as abu, IF(sum(split2_3) IS NULL,0,sum(split2_3)) as split2_3, IF(sum(split1_2) IS NULL, 0, sum(split2_3)) as split1_2, IF(sum(lpa) IS NULL,0,sum(lpa)) as lpa'))
      ->whereYear('tgl_transaksi','=',$input['tahun'])
      ->groupBy(DB::raw('DATE_FORMAT(tgl_transaksi, "%v/%x")'))
      ->orderby('tgl_transaksi','ASC')
      ->get();

      $total_pasir = array();
      $total_abu = array();
      $total_gendol = array();
      $total_split_1 = array();
      $total_split_2 = array();
      $total_lpa = array();

      if(isset($penjualan_barang) && !$penjualan_barang->isEmpty())
      {
        foreach($penjualan_barang as $key => $val)
        {
          array_push($total_pasir,$val->pasir);
          array_push($total_abu,$val->abu);
          array_push($total_gendol,$val->gendol);
          array_push($total_split_1,$val->split1_2);
          array_push($total_split_2,$val->split2_3);
          array_push($total_lpa,$val->lpa);
        }
      }

      $graph_pie=array();

      $total_pasir_pie=RawData::select(DB::raw('count(id) as pasir'))
      ->whereNotNull('pasir')
      ->where('campur','N')
      ->whereYear('tgl_transaksi',date(''.$input['tahun'].''))
      ->first();

      array_push($graph_pie, $total_pasir_pie->pasir);

      $total_abu_pie=RawData::select(DB::raw('count(id) as abu'))
      ->whereNotNull('abu')
      ->where('campur','N')
      ->whereYear('tgl_transaksi',date(''.$input['tahun'].''))->first();
      array_push($graph_pie, $total_abu_pie->abu);

      $total_gendol_pie=RawData::select(DB::raw('count(id) as gendol'))
      ->whereNotNull('gendol')
      ->where('campur','N')
      ->whereYear('tgl_transaksi',date(''.$input['tahun'].''))->first();
      array_push($graph_pie, $total_gendol_pie->gendol);

      $total_split_1_pie=RawData::select(DB::raw('count(id) as split1_2'))
      ->whereNotNull('split1_2')
      ->where('campur','N')
      ->whereYear('tgl_transaksi',date(''.$input['tahun'].''))->first();
      array_push($graph_pie, $total_split_1_pie->split1_2);

      $total_split_2_pie=RawData::select(DB::raw('count(id) as split2_3'))
      ->whereNotNull('split2_3')
      ->where('campur','N')
      ->whereYear('tgl_transaksi',date(''.$input['tahun'].''))->first();
      array_push($graph_pie, $total_split_2_pie->split2_3);

      $total_lpa_pie=RawData::select(DB::raw('count(id) as lpa'))
      ->whereNotNull('lpa')
      ->where('campur','N')
      ->whereYear('tgl_transaksi',date(''.$input['tahun'].''))->first();
      array_push($graph_pie, $total_lpa_pie->lpa);

      $total_campur_pie=RawData::select(DB::raw('count(id) as campur'))
      ->where('campur','Y')
      ->whereYear('tgl_transaksi',date(''.$input['tahun'].''))->first();
      array_push($graph_pie, $total_campur_pie->campur);

      $label_pie=['Pasir','Abu','Pasir Gendol','Split 1/2','Split 2/3','LPA','Campur'];
      
      return response()->json(array('minggu' => $minggu, 'total_transaksi' => $total_transaksi, 'total_pasir' => $total_pasir, 'total_abu' => $total_abu, 'total_gendol' => $total_gendol, 'total_split_1' => $total_split_1, 'total_split_2' => $total_split_2, 'total_lpa' => $total_lpa,'graph_pie' => $graph_pie, 'label_pie' => $label_pie));
    }

     public function create(Request $request)
     {
       $data = new RawData;
       $data->tgl_transaksi= $request->tgl_transaksi;
       $data->no_nota = $request->no_nota;
       $data->pasir= $request->pasir;
       $data->gendol= $request->gendol;
       $data->abu= $request->abu;
       $data->split2_3= $request->split2_3;
       $data->split1_2= $request->split1_2;
       $data->lpa= $request->lpa;
       $data->campur= $request->campur;

       $data->created_at= date('Y-m-d H:i:s');
       $data->updated_at= date('Y-m-d H:i:s');
       
       $data->save();
       return response()->json($data);
     }

     public function show($id)
     {
        $product = Product::find($id);
        return response()->json($product);
     }

     public function update(Request $request, $id)
     { 
        $product= Product::find($id);
        
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->description = $request->input('description');
        $product->save();
        return response()->json($product);
     }

     public function destroy($id)
     {
        $product = RawData::find($id);
        $product->delete();
        return response()->json('product removed successfully');
     }
}
