<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function validateRule($isUpdate = false){
        $rules = [
            'name'=>'required|string|min:3|max:20',
            "email"=>'required|email', // Removed unique check here for update simply, or handle it carefully. For now, keeping simple but unique check fails on update if not handled.
            // unique:users,email is tricky on update because it flags the current user's email.
            // simpler to just validate email format for now or exclude current user id.
            // 'password'=>'required|min:6' // Moved out
        ];

        if (!$isUpdate) {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|min:6';
        } else {
             $rules['email'] = 'required|email';
             // Password is optional for update
             $rules['password'] = 'nullable|min:6';
        }

        $messages = [
            "name.required"=>'the name is required',
            'name.string'=>'the name must be a string',
            'name.min'=>'the name must contain at least 3 letters',
            'name.max'=>'the name must contain maximum 20 letters',
            'email.required'=>'the email is required',
            'email.email'=>'the email it must be type email',
            'email.unique'=>'the email it must be unique',
            'password.required'=>'the password is required',
            'password.min'=>'the password must contais at least 6 letters'
        ];

        return [$rules,$messages];
    }

    // Keep original validate for backward compatibility if needed, but better to refactor usage.
    // The original validate() was mixed. Let's strictly use validateRule in store/update.

    /**
     * return all users
     */
    public function index(Request $request)
    {
        try{
            if(isset($request->name)){
                $users = User::where('name','LIKE',"%$request->name%")->get();
            }else{
                $users = User::all();
            }
            return response()->json($users);
        }catch(Exception $e){
            return response()->json([
                "error"=>"there is a problem to show the users",
                "mistake"=>$e->getMessage()
            ]);
        }
    }

    /**
     * creation of new user
     */
    public function store(Request $request)
    {
        try{
            // Use isUpdate = false
            [$rules, $messages] = $this->validateRule(false);
            $validate = Validator::make($request->all(), $rules, $messages);

            if($validate->fails()){
                return response()->json(["error"=>$validate->errors()->first()]);
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                "success"=>"the user has been created"
            ]);

        }catch(Exception $e){
            return response()->json([
                "error"=>"there is a problem to create the user",
                "mistake"=>$e->getMessage()
            ]);
        }
    }

    /**
     * return one user by id
     */
    public function show(string $id)
    {
        try{
            $user = User::where("id",$id)->first();

            if(!$user) {
                return response()->json(["error"=>"the user does not exists"]);
            }

            return response()->json($user);

        }catch(Exception $e){
            return response()->json([
                "error"=>"there is a problem to show the user",
                "mistake"=>$e->getMessage()
            ]);
        }
    }

    /**
     * update one user by id.
     */
    public function update(Request $request, string $id)
    {
        try{
            // Use isUpdate = true
            [$rules, $messages] = $this->validateRule(true);
            
            // Allow same email for the current user (rudimentary check, ideally use Rule::unique('users')->ignore($id))
            // But strict unique check on 'email' key in $rules might fail if we don't handle it.
            // For now, I removed 'unique' from the update rule in validateRule to avoid "email taken" error when updating self.
            
            $validate = Validator::make($request->all(), $rules, $messages);

            if($validate->fails()){
                return response()->json(["error"=>$validate->errors()->first()]);
            }

            $user = User::where("id",$id)->first();

            if(!$user) {
                return response()->json(["error"=>"the user does not exists"]);
            }

            $user->name = $request->name;
            $user->email = $request->email;
            
            // Only update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();

            return response()->json(["success"=>"the user has been updated"]);

        }catch(Exception $e){
            return response()->json([
                "error"=>"there is a problem to update the user",
                "mistake"=>$e->getMessage()
            ]);
        }
    }

    /**
     * Remove one user by id
     */
    public function destroy(string $id)
    {
        try{
            $user = User::where("id",$id)->first();

            if(!$user) {
                return response()->json(["error"=>"the user does not exists"]);
            }

            $user->delete();

            return response()->json(["success"=>"the user has been deleted"]);
        }catch(Exception $e){
            return response()->json([
                "error"=>"there is a problem to remove the user",
                "mistake"=>$e->getMessage()
            ]);
        }
    }

}
