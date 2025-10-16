<?php

namespace App\Http\Controllers;

use App\Models\Modifier;
use App\Models\User;
use App\Models\UserRole;
use App\Rules\UniqueNameForLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::get();
        return view('people/users/user-index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userRoles = UserRole::get();
        if ($this->isAdmin()) {
            $data = session()->get('localization_for_changes_data');
            return view($data ? 'people/users/user-create' : 'pop-up-locations', compact('userRoles'));
        }
        return view('people/users/user-create', compact('userRoles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'pin' => ['integer', 'digits_between:4,4', new UniqueNameForLocation(User::class)],
                'role_id' => 'required'
            ]);
            $data = $request->all();
            $data['random_id'] = time();
            $data['id'] = Uuid::uuid4()->toString();
            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            $data['password'] = Hash::make($request->password);
            DB::table('users')->insert($data);
            return response()->json(['success', 'User Created']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $person = $user;
        $userRoles = UserRole::get();
        return view('people/users/user-edit', compact('person', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => ['required', Rule::unique('users')->ignore($user->id)],
                'role_id' => 'required',
                'pin' => ['required', 'integer','digits_between:4,4',
                    Rule::unique('users')->where(function ($query) use ($request, $user) {
                        return $query->where('location_id', session()->get('localization_for_changes_data')['id'] ?? auth()->user()->location_id)
                            ->orWhere('location_id', null);
                    })->ignore($user->id),
                ],
            ]);
            $data = $request->all();
            isset($data['status']) ? $data['status'] = 1 : $data['status'] = 0;
            if (!$data['password']) {
                unset($data['password']);
            }
            if (config('constants.role.adminId') == $data['role_id']){
                $data['location_id'] = null;
            }else{
                $request->validate([
                    'location_id' => 'required',
                ]);
            }
            $user->update($data);
            return response()->json(['success', 'Updated User ']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $countUserAdmins = count(UserRole::where('id', config('constants.role.adminId'))->first()->users);
        if (Auth::id() !== $user->id || $countUserAdmins > 1) {
                        $user->delete();
//            $user->forceDelete();
            return response()->json(['success', "User deleted successfully"]);
        }
        return response()->json(['success', "You can't delete your account"]);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
        $authUser = auth()->id();
        $userIds = $request->ids;
        $key = array_search($authUser, $userIds);
        if ($key !== false) {
            unset($userIds[$key]);
        }
        User::whereIn('id', $userIds)->delete();
//        User::whereIn('id', $userIds)->forceDelete();
        return response()->json(['success' => 'The records are trashed'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(Modifier $modifier)
    {
        $modifier->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(Modifier $modifier)
    {
        $modifier->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }
}
