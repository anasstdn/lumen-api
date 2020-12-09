<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
date_default_timezone_set("Asia/Jakarta");

class DepartementController extends Controller
{
    public function getData(Request $request)
    {
        $offset = $request->has('offset') ? $request->get('offset') : 0;
        $limit = $request->has('limit') ? $request->get('limit') : 10;
        $search = $request->has('search') ? $request->get('search') : null;

        $data['data'] = Departement::select('*')
                    ->where(function($q) use($search){
                        if(!empty($search))
                        {
                            $q->where('departement','LIKE','%'.$search.'%');
                        }
                    })
                    ->offset($offset)
                    ->limit($limit)
                    ->get();

        $data['total'] = count(Departement::get());

        return response()->json($data);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'departement' => 'required|string',
            'flag_aktif' => 'required|string',
        ]);

        $data = new Departement;
        $data->departement= $request->departement;
        $data->flag_aktif= $request->flag_aktif;
        $data->created_at= date('Y-m-d H:i:s');
        $data->updated_at= date('Y-m-d H:i:s');

        $data->save();
        return response()->json($data);
    }

    public function edit($id)
    {
        $data = Departement::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
     { 
        $this->validate($request, [
            'departement' => 'required|string',
            'flag_aktif' => 'required|string',
        ]);

        $data= Departement::find($id);
        $data->departement = $request->input('departement');
        $data->flag_aktif = $request->input('flag_aktif');
        $data->updated_at = date('Y-m-d H:i:s');
        $data->save();
        return response()->json($data);
     }

     public function destroy($id)
     {
        $data = Departement::find($id);
        $data->delete();
        return response()->json($data);
     }
}