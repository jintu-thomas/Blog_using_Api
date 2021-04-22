<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
use Validator;



class UserController extends ApiController
{
   

    public function registration(Request $request)
    {

        $validation = Validator::make($request->all(),[

        'first_name' =>'required',
        'last_name' =>'required',
        'email' =>'required|email|unique:users',
        'password' => 'required',
        'c_password' => 'required|same:password',

        ]);

        if($validation->fails()) {
            return response()->json($validation->errors(),202);
        }
        

        $data = $request ->all();

        
        $data['password'] = bcrypt($data['password']);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['admin'] = User::REGULAR_USER;

        $user =User::create($data);

        $resArr = [];

        $resArr['token']=$user->createToken('api-application')->accessToken;
        $resArr['first_name']=$user->first_name;
        return response()->json($resArr,200);
    }



    public function login(Request $request)
    {
        if(Auth::attempt([
            'email'=>$request->email,
            'password'=>$request->password
            ])){
        
                $user = Auth::user();
                $resArr = [];
                $resArr['token']=$user->createToken('api-application')->accessToken;
                $resArr['first_name']=$user->first_name;
                return response()->json($resArr,200);
        }else{
            return  response()->json(['error'=>'Unautherized Access'],203);
        }
    }
    
    public function logout(Request $request)
    {
    
        $token = $request->user()->token();
        $token->revoke();
        $response = ["message" => "you have successfully logout"];
        return response($response,200);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::all();
        return $this->showAll($user);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $user=User::findOrFail($id);
        return $this->showOne($user);
    }

   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        if(Auth::user()->id == $user->id) 
        {
            $rules = [
                'email'=>'email|unique:users,' .$user->id,
                'password'=>'min:6|confirmed',
                'admin'=>'in:' .User::ADMIN_USER . ',' .User::REGULAR_USER,
            ];
            if($request ->has('first_name')){
                $user->first_name=$request->first_name;
            }

            if($request ->has('last_name')){
                $user->last_name=$request->last_name;
            }

            if($request ->has('email') && $user->email !=$request->email) {
                $user->verified = User::UNVERIFIED_USER;
                $user->verification_token = User::generateVerificationCode();
                $user->email=$request->email;
            }
            
            if($request ->has('password')){

                $user->password=bcrypt($request->password);
            }

            if(!$user->isDirty()){
                return$this->errorResponse('you need to specefy a different value to update',422);
            }

            $user->save();
            return $this->showOne($user);
        } 
        else {
            $response = ["message" => "only account owner can edit this account"];
            return response($response,200);

        }
       
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if(Auth::user()->id == $user->id) 
        {
            $user=User::findOrFail($id);
            
            $token = $request->user()->token();
            $token->revoke();
            $response = ["message" => "you have successfully logout"];
            return response($response,200);
            $user->delete();

            return $this->showOne($user);
        }
        else {
            $response = ["message" => "only account owner can delete this account"];
            return response($response,200);
        }
    }
}
