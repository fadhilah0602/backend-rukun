<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::orderBy('created_at', 'DESC')->get();
        $response = [
            'message' => 'List User order by time',
            'data' => $user
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required'],
            'password' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 
            Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $user = User::create($request->all());
            $response = [
                'message' => 'User created',
                'data' => $user
            ];
            return response()->json($response, Response::HTTP_CREATED);
        } catch(QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $response = [
            'message' => 'Detail of user resource',
            'data' => $user
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required'],
            'password' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 
            Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $user->update($request->all());
            $response = [
                'message' => 'User updated',
                'data' => $user
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch(QueryException $e) {

            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        try {
            $user->delete();
            $response = [
                'message' => 'User deleted'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch(QueryException $e) {

            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }

    public $successStatus= 200;

    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('nApp')->accessToken;
            return response()->json(['success' => $success], $this->successStatus);
        }
        else{
            return response()->json(['error' =>'Unauthorized'], 401);
        }
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',

        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('nApp')->accessToken;
        $success['name'] = $user->name;

        return response()->json(['success' => $success], $this->successStatus);
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json(['success'=>$user], $this->successStatus);
    }


}
