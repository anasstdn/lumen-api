<?php

namespace App\Http\Controllers;

use App\Models\StatusPerkawinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
date_default_timezone_set("Asia/Jakarta");

class StatusPerkawinanController extends Controller
{
    public function getData(Request $request)
    {
        $offset = $request->has('offset') ? $request->get('offset') : 0;
        $limit = $request->has('limit') ? $request->get('limit') : 10;
        $search = $request->has('search') ? $request->get('search') : null;

        $data['data'] = StatusPerkawinan::select('*')
                    ->where(function($q) use($search){
                        if(!empty($search))
                        {
                            $q->where('status_perkawinan','LIKE','%'.$search.'%');
                        }
                    })
                    ->offset($offset)
                    ->limit($limit)
                    ->get();

        $total_all = StatusPerkawinan::select('*')
                    ->where(function($q) use($search){
                        if(!empty($search))
                        {
                            $q->where('status_perkawinan','LIKE','%'.$search.'%');
                        }
                    })
                    ->get();

        $data['total'] = count($total_all);

        return response()->json($data);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'status_perkawinan' => 'required|string',
            'flag_aktif' => 'required|string',
        ]);

        $data = new StatusPerkawinan;
        $data->status_perkawinan= $request->status_perkawinan;
        $data->flag_aktif= $request->flag_aktif;
        $data->created_at= date('Y-m-d H:i:s');
        $data->updated_at= date('Y-m-d H:i:s');

        $data->save();
        return response()->json($data);
    }

    public function edit($id)
    {
        $data = StatusPerkawinan::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
     { 
        $this->validate($request, [
            'status_perkawinan' => 'required|string',
            'flag_aktif' => 'required|string',
        ]);

        $data= StatusPerkawinan::find($id);
        $data->status_perkawinan = $request->input('status_perkawinan');
        $data->flag_aktif = $request->input('flag_aktif');
        $data->updated_at = date('Y-m-d H:i:s');
        $data->save();
        return response()->json($data);
     }

     public function destroy($id)
     {
        $data = StatusPerkawinan::find($id);
        $data->delete();
        return response()->json($data);
     }
}