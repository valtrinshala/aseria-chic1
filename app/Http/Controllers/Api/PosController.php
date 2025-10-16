<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseModel;
use App\Http\Controllers\Controller;
use App\Models\AndroidModels\IncomingRequest;
use App\Models\AndroidModels\Pos;
use App\Models\AndroidModels\PosIncomingRequest;
use App\Models\Setting;
use App\Models\User;
use App\Services\LogoutDevicesUsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class PosController extends Controller
{
    protected $logoutDevicesUsersService;
    public function __construct(LogoutDevicesUsersService $logoutDevicesUsersService)
    {
        $this->logoutDevicesUsersService = $logoutDevicesUsersService;
    }
    public function userPosLogin(Request $request)
    {
        try {
            $credentials = $request->validate([
                'user_id' => 'required',
                'pin' => 'required'
            ]);
            $checkUser = User::where(['id' => $credentials['user_id'], 'pin' => $credentials['pin']])->first();
            if ($checkUser && Auth::loginUsingId($credentials['user_id'])) {
                $user = Auth::user();
                $token = $user->createToken('API Token')->plainTextToken;
                return ResponseModel::success(['token' => $token]);
            }
            return ResponseModel::error(__('Invalid credentials'));
        } catch (ValidationException $e) {
            return ResponseModel::error($e->getMessage());
        }
    }

    public function devicePosLogin(Request $request)
    {
        try {
            $request->validate([
                'device_id' => 'required',
                'authentication_code' => 'required'
            ]);
            $deviceId = $request->device_id;
            $authenticationCode = $request->authentication_code;
            $pos = Pos::where(['pos_id' => $deviceId, 'authentication_code' => $authenticationCode, 'status' => true])->first();
            if ($pos) {
                $token = $pos->createToken($pos->name . '-AuthToken', ['pos'])->plainTextToken;
                return ResponseModel::success(['token' => $token]);
            } else {
                $pos = Pos::where(['pos_id' => $deviceId, 'status' => true])->first();
                if ($pos) {
                    return ResponseModel::error(__('Authentication code is invalid!'), 404);
                }
            }

            $checkIncomingRequest = IncomingRequest::where('device_id', $deviceId)->first();
            if ($checkIncomingRequest) {
                return ResponseModel::error(__('You have requested to register, but your request is still pending!'), 404);
            }
            return ResponseModel::error(__('You have not made a request to register'), 401);
        } catch (ValidationException $e) {
            return ResponseModel::error($e->getMessage(), 422);
        }
    }

    public function devicePosRegister(Request $request)
    {
        try {
            $request->validate([
                'device_id' => 'required|unique:pos,pos_id|unique:pos_incoming_requests',
                'authentication_code' => 'required'
            ]);
            $data = $request->all();
            $auth_code = Setting::first()->auth_code_for_e_kiosks;
            if ($data['authentication_code'] !== $auth_code) {
                return ResponseModel::error(__('Authentication code is invalid!'), 401);
            }
            $register = PosIncomingRequest::create($data);
            if ($register) {
                return ResponseModel::success();
            }
            return ResponseModel::error(__('Something went wrong on the server!'), 422);
        } catch (ValidationException $e) {
//            dd();
            return ResponseModel::error(
                isset($e->validator->getMessageBag()->getMessages()['device_id'][0]) ?
                    $e->validator->getMessageBag()->getMessages()['device_id'][0] :
                    $e->validator->errors(), 400);
        }catch (\Exception $e) {
            return ResponseModel::error($e->getPrevious(), 422);
        }
    }
    public function logout(Request $request)
    {
        $routeName = \Route::currentRouteName();
        if ($routeName === 'pos.user.logout'){
            $token = $request->bearerToken();
        } else if ($routeName === 'pos.device.logout'){
            $token = $request->header('Device-Token');
        } else {
            return ResponseModel::error(__('Invalid logout request'), 400);
        }
        return $this->logoutDevicesUsersService->logout($token);
    }
    public function users()
    {
        try {
            $users = User::where(['role_id' => config('constants.role.orderTakerId'), 'status' => true])->select('id', 'email', 'language')->get();
            return ResponseModel::success(['users' => $users]);
        } catch (\Exception $exception){
            return ResponseModel::error($exception->getMessage());
        }
    }
}
