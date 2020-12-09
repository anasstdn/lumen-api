<?php

namespace App\Http\Controllers;

use App\Models\Agama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
date_default_timezone_set("Asia/Jakarta");

class AgamaController extends Controller
{
    public function getData(Request $request)
    {
        $offset = $request->has('offset') ? $request->get('offset') : 0;
        $limit = $request->has('limit') ? $request->get('limit') : 10;
        $search = $request->has('search') ? $request->get('search') : null;

        $data['data'] = Agama::select('*')
                    ->where(function($q) use($search){
                        if(!empty($search))
                        {
                            $q->where('agama','LIKE','%'.$search.'%');
                        }
                    })
                    ->offset($offset)
                    ->limit($limit)
                    ->get();

        $data['total'] = count(Agama::get());

        return response()->json($data);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'agama' => 'required|string',
            'flag_aktif' => 'required|string',
        ]);

        $data = new Agama;
        $data->agama= $request->agama;
        $data->flag_aktif= $request->flag_aktif;
        $data->created_at= date('Y-m-d H:i:s');
        $data->updated_at= date('Y-m-d H:i:s');

        $data->save();
        return response()->json($data);
    }

    public function edit($id)
    {
        $data = Agama::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
     { 
        $this->validate($request, [
            'agama' => 'required|string',
            'flag_aktif' => 'required|string',
        ]);

        $data= Agama::find($id);
        $data->agama = $request->input('agama');
        $data->flag_aktif = $request->input('flag_aktif');
        $data->updated_at = date('Y-m-d H:i:s');
        $data->save();
        return response()->json($data);
     }

     public function destroy($id)
     {
        $data = Agama::find($id);
        $data->delete();
        return response()->json($data);
     }
}