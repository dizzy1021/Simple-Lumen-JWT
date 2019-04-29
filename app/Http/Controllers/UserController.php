<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class UserController extends BaseController
{
    public function __construct()
    {
        # code...
    }

    public function index()
    {
        $users = User::all();

        return response()->json($users);
    }

    public function create(Request $request)
    {
        $this->validateRequest($request);

        $user = new User;

        $result = $this->payload($request,$user);
        
        return response()->json($result);

    }

    public function show($id)
    {
        $user = User::find($id);

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $constraint = [
            'username' => "required|min:5|max:50|alpha_dash|unique:users,username,$user->id",
            'email' => "required|email|unique:users,email,$user->id",
        ];

        $this->validateRequest($request,$constraint);

        $result = $this->payload($request,$user);

        return response()->json($result);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        $user->delete();

        return response()->json('removed Successfully');
    }

    public function validateRequest(Request $request, $constraint = ['false'])
    {
        $valid = [
            'firstname' => 'required|max:25|min:2|alpha',
            'lastname' => 'required|max:25|min:2|alpha',
            'username' => 'required|unique:users,username|min:5|max:50|alpha_dash',
            'password' => 'required|max:50|min:8',
            'email' => 'required|unique:users,email|email',
            'bio' => 'nullable|min:5|max:255',
            'image' => 'nullable|image|dimensions:min_width=300,min_height=200|max:1024'
        ];

        if($constraint != ['false'])
        {
            foreach ($constraint as $key => $value) {
                $valid[$key] = $value;
            }
        }

        $validatedData = $this->validate($request,$valid);
    }

    public function authenticate(Request $request)
    {
        // Check request is email / username
        if($request->has('email'))
        {
            $this->validate($request,[
                'email' => 'required|email',
            ]);
            $credential = $request->input('email');
        }
        else if($request->has('username'))
        {
            $this->validate($request,[
                'username' => 'required|alpha_dash|min:5|max:50',
            ]);
            $credential = $request->input('username');
        }

            $this->validate($request, [
                'password' => 'required|min:8|max:50;'
            ]);

        // Check exist data
        $user = User::Where('email',$credential)
                    ->orWhere('username',$credential)->first();

        // Throw error if doesnt exist username / email
        if(!$user)
        {
            return response()->json([
                'error' => 'Email/Username does not exist.'
            ], 400);
        }

        // Check password
        if(Hash::check($request->input('password'),$user->password))
        {
            $token = $this->generateToken($user->id);
            $user->api_token = $token;

            $user->save();

            return response()->json([
                'token' => $token,
            ], 200);
        }

        // Return bad request if wrong password / email
        return response()->json([
            'error' => 'Email or password is wrong.'
        ], 400);

    }

    public function generateToken($id)
    {
        $key = env('APP_KEY');
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60*60 // Expiration time
        ];

        return JWT::encode($payload, env('JWT_SECRET'));
    }

    public function payload(Request $request, $user)
    {
        
        $user->firstname= $request->input('firstname');
        $user->lastname= $request->input('lastname');
        $user->username= $request->input('username');
        $user->password = Hash::make($request->input('password'));
        $user->email = $request->input('email');
        $user->bio = $request->input('bio') || null;
        $user->image = $request->file('image') || null;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $destinationPath = '/uploads/user_files/'.$request->input('username');
            $fileName = uniqid().'_'.$request->file('image')->getClientOriginalName().'_'.time();
            $request->file('image')->move($destinationPath, $fileName);
        }

        $user->save();

        return $user;
    }

}
