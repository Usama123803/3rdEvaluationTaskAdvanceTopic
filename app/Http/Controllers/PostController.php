<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\postRequest;
use App\Models\Post;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PostController extends Controller
{
    public function create(postRequest $request) {
        try{
            //Decode in Middleware
            $request->dataMiddleware=(array)$request->dataMiddleware;
            $userID = $request->dataMiddleware['data'];
            
            $post = new Post;
            $post->user_id      = $userID;
            $post->title        = $request->title;
            $post->description  = $request->description;
            $post->digital_data = $request->file('digital_data')->store('folderForFile');
            $create = $post->save();
            
            if($create){
                return response([
                    'status'  => 200,
                    'message' => 'Post Ceated'
                ]);
            }else{
                return response([
                    'status'  => 404,
                    'message' => 'Post not Ceated'
                ]);
            }
        } catch(Throwable $e){
            return response(['message' => $e->getMessage()]);
        }
    }

    public function show(Request $request) {
        try{    
            //Decode in Middleware
            $request->dataMiddleware=(array)$request->dataMiddleware;
            $userID = $request->dataMiddleware['data'];

            return Post::all()->where('user_id',$userID);
        } catch(Throwable $e){
            return response(['message' => $e->getMessage()]);
        }
    }

    public function update(Request $request , $id) {
        try{
            //Decode in Middleware
            $request->dataMiddleware=(array)$request->dataMiddleware;
            $userID = $request->dataMiddleware['data'];

            $post = Post::where('user_id',$userID)->where('id',$id)->first();
            $update = $post->update($request->all());

            if($update){
                return response([
                    'status'  => 200,
                    'message' => 'Post Updated'
            ]);
            }else{
                return response([
                    'status'  => 404,
                    'message' => 'Post not Updated'
                ]);
            }
        } catch(Throwable $e){
            return response(['message' => $e->getMessage()]);
        }
    }

    public function delete(Request $request , $id) {
        try{
            //Decode in Middleware
            $request->dataMiddleware=(array)$request->dataMiddleware;
            $userID = $request->dataMiddleware['data'];

            $post = Post::find($id);
            $delete = $post->delete();

            if($delete){
                return response([
                    'status'  => 200,
                    'message' => 'Post deleted'
                ]);
            }else{
                return response([
                    'status'  => 404,
                    'message' => 'Post not deleted'
                ]);
            }
        } catch(Throwable $e){
            return response(['message' => $e->getMessage()]);
        }
    }
}
