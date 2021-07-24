<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Vaksin;
use JWTAuth;
use DB;

class VaksinController extends Controller
{
    public $response;
    public $user;
    public function __construct(){
        $this->response = new ResponseHelper();

        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function getAllVaksin($limit = NULL, $offset = NULL)
    {
        if($this->user->level == 'masyarakat'){
            $data["count"] = Vaksin::where('id_user', '=', $this->user->id)->count();

            if($limit == NULL && $offset == NULL){
                $data["vaksin"] = Vaksin::where('id_user', '=', $this->user->id)->orderBy('tgl_vaksin', 'desc')->with('kategori', 'tanggapan', 'user')->get();
            } else {
                $data["vaksin"] = Vaksin::where('id_user', '=', $this->user->id)->orderBy('tgl_vaksin', 'desc')->with('kategori', 'tanggapan', 'user')->take($limit)->skip($offset)->get();
            }
        } else {
            $data["count"] = Vaksin::count();

            if($limit == NULL && $offset == NULL){
                $data["vaksin"] = Vaksin::orderBy('tgl_vaksin', 'desc')->with('kategori','tanggapan', 'user')->get();
            } else {
                $data["vaksin"] = Vaksin::orderBy('tgl_vaksin', 'desc')->with('kategori','tanggapan', 'user')->take($limit)->skip($offset)->get();
            }
        }

        return $this->response->successData($data);
    }

    public function getById($id)
    {   
        $data["vaksin"] = Vaksin::where('id_vaksin', $id)->with(['kategori','tanggapan'])->get();

        return $this->response->successData($data);
    }

    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'tgl_vaksin' => 'required|date_format:Y-m-d',
			'lokasi' => 'required|string',
			'id_kategori' => 'required',
			'foto' => 'required',
		]);

		if($validator->fails()){
            return $this->response->errorResponse($validator->errors());
		}

        $foto = rand().$request->file('foto')->getClientOriginalName();
        $request->file('foto')->move(base_path("./public/uploads"), $foto);

		$vaksin = new Vaksin();
		$vaksin ->id_user         = $this->user->id;
		$vaksin ->id_kategori     = $request->id_kategori;
		$vaksin ->tgl_vaksin      = $request->tgl_vaksin;
		$vaksin ->lokasi          = $request->lokasi;
        $vaksin ->foto            = $foto;
        $vaksin ->status          = 'terkirim';
		$vaksin ->save();

        $data = Vaksin::where('id_vaksin','=', $vaksin->id)->first();
        return $this->response->successResponseData('Data Vaksin berhasil terkirim', $data);
    }

    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'id_vaksin' => 'required',
			'status' => 'required|string',
		]);

		if($validator->fails()){
            return $this->response->errorResponse($validator->errors());
		}

		$vaksin         = vaksin::where('id_vaksin', $request->id_vaksin)->first();
		$vaksin->status  = $request->status;
		$vaksin->save();

        return $this->response->successResponse('Status berhasil diubah');
    }

    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'tahun' => 'required|numeric',
		]);

		if($validator->fails()){
            return $this->response->errorResponse($validator->errors());
		}

        $query = DB::table('vaksin')
                    ->select('vaksin.tgl_vaksin', 'vaksin.lokasi', 'vaksin.status', 'kategori.nama_kategori', 'users.nama')
                    ->join('users', 'users.id', '=', 'vaksin.id_user')
                    ->join('kategori', 'kategori.id_kategori', '=', 'vaksin.id_kategori')
                    ->whereYear('vaksin.tgl_vaksin', '=', $request->tahun);

        if($request->bulan != NULL){
            $query->WhereMonth('vaksin.tgl_vaksin', '=', $request->bulan);
        }
        if($request->tgl != NULL){
            $query->WhereDay('vaksin.tgl_vaksin', '=', $request->tgl);
        }
        
        $data = $query->get();
        
        return $this->response->successData($data);
    }
    

}
