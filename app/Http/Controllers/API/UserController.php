<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image as Image;

class UserController extends Controller
{   

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>'required|string|max:191',
            'email'=>'required|string|email|max:191|unique:users',
            'password'=>'required|string|min:6',

        ]);
        return User::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
       $user=User::findorFail($id);

       $this->validate($request,[
        'name'=>'required|string|max:191',
        // update email even though unique
        'email'=>'required|string|email|max:191|unique:users,email,'.$user->id,
        'password'=>'sometimes|string|min:6',

    ]);
       $user->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function profile()
    {   
        //api -authenticated user
        return auth('api')->user();
    }

    public function updateProfile(Request $request)
    {   
        $user =auth('api')->user();
        // wouldve just used cloudinary or aws s3 but must learn base65 :C
        // add timestamp to base64 link to make  unique //naming
        // explode takes string convert into array,each word idmade into array
        // substr returns position of second param as number

        if($request->photo){
            // $image = time().'.' .explode('/', explode(':', substr($request->photo,0,strpos($request->photo, ':')))[1])[1];
            $image = explode('/', mime_content_type($request->photo))[1];
            Image::make($request->photo)->save(public_path('image/profile/').$image);
        }
    }
    public function destroy($id)
    {
        $user = User::findorFail($id);

        //delete user
        $user->delete();

        return[ 'message'=>'User Deleted'];
    }

    
}
