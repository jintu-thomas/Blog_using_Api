<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Support\Facades\Route;

class PostController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $post = Post::all();
        return $this->showAll($post);
    }

   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    
        $validation = Validator::make($request->all(),[
            'title'=>'required|max:200',
            'description'=>'required|max:200',
            'content'=>'required|max:200',
            'image_path'=>'required',
            'visible'=>'required'
        ]);

        if($validation->fails()) {
            return response()->json($validation->errors(),202);
        }
        
        $data = $request ->all();



        $data['title'] =$request->title;
        $data['description'] = $request->description;
        $data['content'] = $request->content;
        $data['image_path'] = $request->image_path->store('');
        $data['visible'] = $request->visible;
        $data['user_id']= $request->user()->id;

        $post=Post::create($data);
        
        return response()->json(['data'=>$post],201);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);
        return response()->json(['data'=> $post]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {

        if(Auth::user()->id == $post->user_id)
        {
            $rules = [
                'title'=>'required|max:200',
                'description'=>'required|max:200',
                'content'=>'required|max:200',
                'visible'=>'required',
            ];



            if($request->has('title')) {
                $post->title = $request->title;
            }
            
            if($request->has('description')) {
                $post->description = $request->description;
            }
            if($request->has('content')) {
                $post->content = $request->content;
            }
            if($request->hasFile('image_path')) {
                Storage::delete($post->image_path);
                $post->image_path = $request->image_path;

            }

            if($request->has('visible')) {
                $post->visible = $request->visible;
            }

            if ($post -> isClean()) {
                return $this -> errorResponse('You need to specefy a different value to update' , 422);
    
            }


            $post->save();
            return $post;
        } else{
            $response = ["message" => "Only owner can update the post"];
            return response($response,200);
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
        $post=Post::findOrFail($id);

        if(Auth::user()->id == $post->user_id)
        {
            $post->delete();
            Storage::delete($post->image_path);
            return response()->json(['data'=>$post]);
        }else {
            $response = ["message" => "You are not the Owner of the post.only can delete Owner of the post" ];
            return response($response,200);
        }
    }
}
