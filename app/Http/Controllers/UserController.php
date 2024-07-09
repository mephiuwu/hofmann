<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class UserController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    function getData(){
        $users = $this->apiService->getListTableUsers();
        return view('app', compact('users'));
    }

    public function getUserData(){
        $users = $this->apiService->getUsers();
        return response()->json($users);
    }

    public function sendUser(Request $request){
        $data = $request->only(['id', 'code', 'amount', 'date', 'github']);
        $responseStatus = $this->apiService->sendUser($data);
        if(!$responseStatus || $responseStatus != 200) return response()->json(['status' => 'error', 'message' => 'Error al procesar los datos'], 400);

        return response()->json([
            'status' => 'success',
            'message' => 'Datos enviados correctamente',
        ], $responseStatus);
    }
}
