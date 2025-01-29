<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Hash;
use JavaScript;
use Auth;

class UserController extends Controller
{
    /**
     * Renders the "My Profile" page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function profilePage()
    {
        JavaScript::put(['View' => [
            'Routes' => [
                'editProfile' => route('user.ajax.editProfile'),
                'editPassword' => route('user.ajax.editPassword')
            ]
        ]]);
        return view('user.profile',['user' => user()]);
    }

    /**
     * Renders the "All Users" page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function allUsersPage()
    {
        
        JavaScript::put(['View' => [
            'Routes' => [
                'deleteUser' => route('user.ajax.deleteUser'),
            ]
        ]]);
        $users = User::all()->sortByDesc("id");;
        return view('user.all',['users' => $users, 'current_user' => user()]);
    }


    /**
     * Renders the "Single User" page by using user's id
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function singleUserPage($id)
    {   
        
        JavaScript::put(['View' => [
            'Routes' => [
                'editUser' => route('user.ajax.editUser'),
            ]
        ]]);

        $user = User::find($id);
        if($user){
            return view('user.single',['user' => $user]);
        }

    }


    /**
     * Renders the "Register User" page 
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function newUserPage()
    {

        JavaScript::put(['View' => [
            'Routes' => [
                'addUser' => route('user.ajax.newUser'),
                'allUsers' => route('user.all'),
                'deleteUser' => route('user.ajax.deleteUser'),
            ]
        ]]);

        return view('user.new');
    }


    /**
     * Attemtps to add new user (registration)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxNewUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string',
            'access_status' => 'required|string',
        ]);


        // Check if the user with the given email already exists
        $existingUser = User::where('email', $request->get('email'))->first();

        if ($existingUser) {
            // User with this email already exists, handle accordingly
            return new JsonResponse(['error' => 'Email address is already taken. Please use a different email address.'], 300);
        }


        $user = new User;

        // Set user attributes from request data
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->access_status = $request->get('access_status');
        $user->password = Hash::make($request->get('password'));

        // Save the user to the database
        $user->save();

        if($user){
            return new JsonResponse();
        }

        return new JsonResponse(['error' => 'Error in creating user!'], 300);
    }


    /**
     * Attemtps to edit any user (includes blocking the access for users)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxEditUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'access_status' => 'required|string',
        ]);

        $user = User::where('email', $request->get('email'))->first();

        if($user){
            
            $name = $request->get('name');
            if($user->name !== $name) {
                $user->name = $name;
            }
            
            $email = $request->get('email');
            if($user->email !== $email) {
                // enforce unique email
                if(User::whereEmail($email)->whereKeyNot($user->id)->count() > 0) {
                    return new JsonResponse(['error' => 'Email address is already taken. Please use a different email address.'], 300);
                }
                $user->email = $email;
            }


            $access_status = $request->get('access_status');
            if($user->access_status !== $access_status) {
                $user->access_status = $access_status;
            }

            $user->save();
            return new JsonResponse();

        }

        return new JsonResponse(['error' => 'User does not exist!'], 300);
    }


    /**
     * Attemtps to delete any user from the database (without deleting the project right now)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxDeleteUser(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $user = User::find($request->get('id'));

        if($user){

            $user->delete();
            return new JsonResponse();
        }

        return new JsonResponse(['error' => 'Sorry! Could not find the user in the application!'], 300);

    }


    /**
     * Attemtps to edit user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxEditProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $user = user();

        $name = $request->get('name');
        if($user->name !== $name) {
            $user->name = $name;
        }

        $email = $request->get('email');
        if($user->email !== $email) {
            // enforce unique email
            if(User::whereEmail($email)->whereKeyNot($user->id)->count() > 0) {
                return new JsonResponse(['error' => 'Email address is already taken. Please use a different email address.'], 300);
            }
            $user->email = $email;
        }

        $user->save();
        return new JsonResponse();
    }

    /**
     * Attemtps to edit user password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxEditPassword(Request $request)
    {
        $request->validate([
            'password_old' => 'required|string',
            'password' => 'required|string',
            'password_repeat' => 'required|same:password',
        ]);

        $user = user();
        $oldPassword = $request->get('password_old');
        $newPassword = $request->get('password');

        // confirm with old password
        if(!Auth::validate(['email' => $user->email, 'password' => $oldPassword])) {
            return new JsonResponse(['error' => "Please confirm with your valid current password"],300);
        }

        // prevent setting the same password
        if($oldPassword == $newPassword) {
            return new JsonResponse(['error' => "You can't set the same password you're already using."],400);
        }

        // update password
        $user->password = Hash::make($newPassword);
        $user->save();

        return new JsonResponse();
    }
}
