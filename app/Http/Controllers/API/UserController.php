<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image as Image;

class UserController extends Controller
{   

    // public function __construct()
    // {
    //     $this->middleware('auth:api');
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $this->authorize('isAdmin');
        // allow access to admin or author using AUTHORIZING ACTIONS
        if(\Gate::allows('isAdmin') || \Gate::allows('isAuthor') ){
            return User::latest()->paginate(5);
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('isAdmin');
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

    //Admin update user
    public function update(Request $request, $id)
    {
        $this->authorize('isAdmin');
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

    //Use update profile
    public function updateProfile(Request $request)
    {   
        $user =auth('api')->user();
        // wouldve just used cloudinary or aws s3 but must learn base65 :C
        // add timestamp to base64 link to make  unique //naming
        // explode takes string convert into array,each word idmade into array
        // substr returns position of second param as number
        $this->validate($request,[
            'name'=>'required|string|max:191',
            'email'=>'required|string|email|max:191|unique:users,email,'.$user->id,
            'password'=>'sometimes|required|min:6',

        ]);
        $currentPhoto = $user->photo;

        if($request->photo != $currentPhoto){
            // $image = time().'.' .explode('/', explode(':', substr($request->photo,0,strpos($request->photo, ':')))[1])[1];
            $image = time().'.'. explode('/', explode(':',substr($request->photo,0,strpos($request->photo,';')))[1])[1];
            Image::make($request->photo)->save(public_path('image/profile/').$image);

            
            // assign new valuephoto field
            $request->merge(['photo'=>$image]);

            $userPhoto = public_path('image/profile/').$currentPhoto;
            // delete old photo if updated
            if(file_exists($userPhoto)){
                @unlink($userPhoto);
            }
        }

        // encrypt password if changed
        if(!empty($request->password)){
            $request->merge(['password'=>Hash::make($request['password'])]);
        }

        $user->update($request->all());
        return['message'=>"success"];
    }

    // search bar for searching user
    public function search(){
        //get value of q store in search
        //loop go to user call function pass query
        // if name or email === search
        if($search = \Request::get('q')){
            $users = User::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                      ->orWhere('email','LIKE',"%$search%");
                })->paginate(20);
            }else{
                $users =  User::latest()->paginate(5);
            }
            return $users;
        }
    public function destroy($id)
    {
        //only admin can use this  endpoint using helper
        $this->authorize('isAdmin');
        $user = User::findorFail($id);

        //delete user
        $user->delete();

        return[ 'message'=>'User Deleted'];
    }

    
}
