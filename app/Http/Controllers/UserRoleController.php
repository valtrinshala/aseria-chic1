<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userRoles = UserRole::with('users')->get();
        return view('people/user-roles/userRole-index', compact('userRoles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('people/user-roles/userRole-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            unset($data['name']);
            $id = Uuid::uuid4()->toString();
            $name = $request->get('name');
            UserRole::create([
                'id' => $id,
                'name' => $name,
                'permissions' => array_keys($data),
            ]);
            return response()->json(['success', 'The User Role is created']);
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
    public function edit(UserRole $userRole)
    {
        if (config('constants.role.adminId') == $userRole->id){
            return back();
        }
        return view('people/user-roles/userRole-edit', compact('userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserRole $userRole)
    {
        try {
            if (config('constants.role.adminId') == $userRole->id){
                return response()->json(['status' => '401','message' => "you cant update the admin role"]);
            }
            $data = $request->all();
            unset($data['_method'], $data['name']);
            $userRole->name = $request->get('name');
            $userRole->permissions = array_keys($data);
            $userRole->update();
            return response()->json(['success', 'the permissions are updated']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserRole $userRole)
    {
        if ($userRole->isPrime()) {
            return response()->json(['error' => "You can't delete the admin role"], 422);
        }
        if (Auth::id() !== $userRole->id) {
//            $userRole->delete();
            $userRole->forceDelete();
            return response()->json(['success' => 'You deleted the   '.$userRole->name.' Role']);
        }
        return response()->json(['error', "You can't delete your user role account"], 422);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
        $userRoleIds = $request->ids;
        $error = false;
        $userRoles = [];
        foreach ($userRoleIds as $userRoleId){
            $userRole = UserRole::find($userRoleId);
            if ($userRole?->isPrime()) {
                $error = true;
            } elseif ($userRole) {
                $userRoles[] = $userRole->id;
            }
        }
        $statusCode = $error ? 422 : 200;
//        UserRole::whereIn('id', $request->ids)->delete();
        UserRole::whereIn('id', $userRoles)->forceDelete();
        return response()->json(!$error ? (['success' => 'The records are trashed']) : ['error' => 'User roles are deleted except the admin user Role, which you cannot delete'], $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(UserRole $userRole)
    {
        $userRole->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(UserRole $userRole)
    {
        $userRole->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }
}
