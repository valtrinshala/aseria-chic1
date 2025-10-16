<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AndroidModels\EKiosk;
use App\Models\AndroidModels\EKioskAsset;
use App\Models\AndroidModels\FoodCategory;
use App\Models\AndroidModels\FoodItem;
use App\Models\AndroidModels\IncomingRequest;
use App\Models\AndroidModels\Language;
use App\Models\AndroidModels\Meal;
use App\Models\AndroidModels\PaymentMethod;
use App\Models\AndroidModels\Tax;
use App\Models\AndroidModels\PositionAsset;
use App\Models\Ingredient;
use App\Models\AndroidModels\Modifier;
use App\Models\AndroidModels\PrintSettings;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\ZReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;

class EKioskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get all categories",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *           name="language_id",
     *           in="query",
     *           description="It is required to take all categories in a major language",
     *           required=true,
     *           @OA\Schema(type="string", default="a2478e62-64e2-447c-9444-1deb9dc7f8b7")
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function categories()
    {
        $categories = FoodCategory::where(['status' => true, 'category_for_kiosk' => true])->get();
        $dealCategory = [];
        $foodCategory = [];
        $drinkCategory = [];
        foreach ($categories as $category) {
            if ($category->products()->where('status', true)->count() > 0 || $category->deals()->where('status', true)->count()) {
                if ($category->id == config('constants.api.dealId')){
                    $dealCategory[] = $category;
                }elseif ($category->category_for_kitchen === true && $category->category_to_ask_for_extra_kitchen === true){
                    $foodCategory[] = $category;
                }else{
                    $drinkCategory[] = $category;
                }
            }
        }
        $filteredCategories = array_merge(
            $dealCategory,
            $foodCategory,
            $drinkCategory
        );
        return response()->json(['categories' => $filteredCategories]);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{categoryId}",
     *     summary="Get products by category ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         description="ID of the category to fetch products for",
     *         required=true,
     *         @OA\Schema(type="string", default="3a54bd7c-5bfe-4bb5-84c3-4b6b2f1277ba")
     *     ),
     *     @OA\Parameter(
     *           name="language_id",
     *           in="query",
     *           description="It is required to take all products in a major language",
     *           required=true,
     *           @OA\Schema(type="string", default="a2478e62-64e2-447c-9444-1deb9dc7f8b7")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function products($categoryId)
    {
        $category = FoodCategory::where(['id' => $categoryId, 'status' => true, 'category_for_kiosk' => true])->first();
        if (!$category) {
            return response()->json([
                'status' => 404,
                'error' => 'Not Found',
                'message' => __("No category was found with id : ") . $categoryId
            ], 404);
        }
        $products = FoodItem::where('food_category_id', $categoryId)->with('ingredients')->get();
        return response()->json(['products' => $products]);
    }

    /**
     * @OA\Get(
     *     path="/api/deals",
     *     summary="Get all deals",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *           name="language_id",
     *           in="query",
     *           description="It is required to take all deals in a major language",
     *           required=true,
     *           @OA\Schema(type="string", default="a2478e62-64e2-447c-9444-1deb9dc7f8b7")
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function deals()
    {
        $deals = Meal::where('status', true)->with('foodItems', 'foodItems.ingredients')->get();
        return response()->json(['deals' => $deals]);
    }

    /**
     * @OA\Get(
     *     path="/api/printDevices",
     *     summary="Get all devices",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function devices()
    {
        $devices = PrintSettings::where(['cash_register_or_e_kiosk_assigned' => false, 'device_type' => 'terminal'])->get();
        return response()->json(['devices' => $devices]);
    }

    /**
     * @OA\Post(
     *     path="/api/assignDevices",
     *     summary="Set selected devices, so that we can identify which device is connected to which eKiosk",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"device_ids"},
     *                 @OA\Property(property="device_ids", type="array",
     *                      @OA\Items(
     *                          type="string"
     *                      ),
     *                      example={"device_id1", "device_id2"}
     *                  )
     *             )
     *         )
     * *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function assignDevices(Request $request)
    {
        $data = $request->all();
        $message = __('These devices are used: ');
        $usedDevices = false;
        foreach ($data['device_ids'] as $deviceId) {
            $device = \App\Models\PrintSettings::where(['id' => $deviceId])->first();
            if ($device && !$device->cash_register_or_e_kiosk_assigned) {
                $device->cash_register_or_e_kiosk = 'e_kiosk';
                $device->e_kiosk_id = auth()->user()->id;
                $device->cash_register_or_e_kiosk_assigned = true;
                $device->update();
            } else {
                $message .= $device?->device_name . '(' . $device?->device_ip . '), ';
                $usedDevices = true;
            }
        }
        if ($usedDevices) {
            return response()->json(['status' => 1, 'message' => rtrim($message, ', ')]);
        }
        return response()->json(['status' => 0, 'message' => 'success']);
    }

    /**
     * @OA\Get(
     *     path="/api/modifiers/{categoryId}",
     *     summary="Get products by category ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         description="ID of the category to fetch modifiers for",
     *         required=true,
     *         @OA\Schema(type="string", default="3a54bd7c-5bfe-4bb5-84c3-4b6b2f1277ba")
     *     ),
     *     @OA\Parameter(
     *           name="language_id",
     *           in="query",
     *           description="It is required to take all products in a major language",
     *           required=true,
     *           @OA\Schema(type="string", default="a2478e62-64e2-447c-9444-1deb9dc7f8b7")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function modifiers($categoryId)
    {
        $category = FoodCategory::where(['id' => $categoryId, 'status' => true, 'category_for_kiosk' => true])->find($categoryId);
        if (!$category) {
            return response()->json([
                'status' => 404,
                'error' => 'Not Found',
                'message' => __("No category was found with id : ") . $categoryId
            ], 404);
        }
        return response()->json(['modifiers' => $category->modifiers]);
    }

    /**
     * @OA\Get(
     *     path="/api/allModifiers",
     *     summary="Get all modifiers",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *           name="language_id",
     *           in="query",
     *           description="It is required to take all modifiers in a major language",
     *           required=true,
     *           @OA\Schema(type="string", default="a2478e62-64e2-447c-9444-1deb9dc7f8b7")
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function allModifiers()
    {
        $modifiers = Modifier::where('status', true)
            ->with('category:id')
            ->get()
            ->map(function ($modifier) {
                $modifier->category_ids = $modifier->category->pluck('id')->toArray();
                unset($modifier->category);
                unset($modifier->translate);
                return $modifier;
            });
        $modifiersArray = $modifiers->toArray();
        return response()->json(['modifiers' => $modifiersArray]);
    }

    /**
     * @OA\Get(
     *     path="/api/paymentMethods",
     *     summary="Get all payment methods",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function paymentMethods()
    {
        $cashId = config('constants.paymentMethod.paymentMethodCashId');
        $cardId = config('constants.paymentMethod.paymentMethodCardId');
        $paymentMethods = PaymentMethod::whereIn('id', [$cashId, $cardId])->where('status', true)->get();
        return response()->json(['paymentMethods' => $paymentMethods]);
    }

    /**
     * @OA\Get(
     *     path="/api/languages",
     *     summary="Get all languages",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function languages()
    {
        $languages = Language::get();
        if (count($languages) <= 0) {
            return response()->json([
                'status' => 404,
                'error' => __('Not Found'),
                'message' => __("No language found!")
            ], 404);
        }
        return response()->json(['languages' => $languages]);
    }


    /**
     * @OA\Get(
     *     path="/api/languages/{languageId}",
     *     summary="Get all keys(for translation) for a language with language ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="languageId",
     *         in="path",
     *         description="ID of the language to fetch keys for",
     *         required=true,
     *         @OA\Schema(type="string", default="a2478e62-64e2-447c-9444-1deb9dc7f8b7")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function language($languageId)
    {
        $locale = Language::find($languageId)?->locale;
        $jsonFilePath = resource_path('lang/android/' . $locale . '.json');
        if (File::exists($jsonFilePath)) {
            $jsonContent = File::get($jsonFilePath);
            $jsonData = json_decode($jsonContent, true);
            return response()->json($jsonData);
        } else {
            return response()->json(['status' => 404, 'message' => __("Language keys doesn't exist")]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/tax/{type}",
     *     summary="Get tax by type",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         description="type(dine_in, take_away, delivery(Unsupported)) of the tax to fetch keys for",
     *         required=true,
     *         @OA\Schema(type="string", default="dine_in")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function tax($type)
    {
        $tax = Tax::where(['type' => $type])->first();
        if (!$tax) {
            return response()->json([
                'status' => 404,
                'error' => 'Not Found',
                'message' => __("No tax found!")
            ], 404);
        }
        return response()->json(['tax' => $tax]);
    }

    /**
     * @OA\Get(
     *     path="/api/getAsset",
     *     summary="Get an asset with asset key",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *           name="assetName",
     *           in="query",
     *           description="It is required to take an img of the asset",
     *           required=true,
     *           @OA\Schema(type="string", default="company_logo")
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function asset(Request $request)
    {
        $assetId = PositionAsset::where('asset_key', $request->assetName)
            ->where('status', true)
            ->first()?->id;

        if (!$assetId) {
            return response()->json([
                'status' => 404,
                'error' => 'Not Found',
                'message' => __("No asset found!")
            ], 404);
        }

        $ekAsset = EKioskAsset::where(['e_kiosk_id' => auth()->user()->id, 'position_id' => $assetId])->first();
        if (!$ekAsset) {
            return response()->json([
                'status' => 404,
                'error' => 'Not Found',
                'message' => __("No asset found!")
            ], 404);
        }

        $data = [
            'assetType' => $ekAsset->type,
            'assetUrl' => $ekAsset->image ? $ekAsset->image : null
        ];
        return response()->json($data);
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *           name="language_id",
     *           in="query",
     *           description="It is required to take all products in a major language",
     *           required=true,
     *           @OA\Schema(type="string", default="a2478e62-64e2-447c-9444-1deb9dc7f8b7")
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function allProducts()
    {
        $allProducts = FoodItem::where('status', true)->with('ingredients')->get();
        return response()->json(['products' => $allProducts]);
    }

    /**
     * @OA\Get(
     *     path="/api/currency",
     *     summary="Get actual currency",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function getCurrency()
    {
        $currency = Setting::first(['currency_symbol_on_left', 'currency_symbol']);
        return response()->json(['currency' => $currency], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="To make a request to register, you must provide the auth code that you receive from the administrator, and after the request is made, you must wait until it is approved.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"device_id", "authentication_code"},
     *                 @OA\Property(property="device_id", type="string", example="fb5c85e29caf6b73"),
     *                 @OA\Property(property="authentication_code", type="string", example="11002233")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User's current location",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="number", example="0, 1 or 2"),
     *             @OA\Property(property="message", type="string", example="Error message"),
     *             @OA\Property(property="access_token", type="string", example="50|JfWa9kgeeyOrj9LJyLWsA59WECx56Gfk20DD48Z325c29dd")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'device_id' => 'required|unique:e_kiosks,e_kiosk_id',
                'authentication_code' => 'required'
            ]);
            $data = $request->all();
            $auth_code = Setting::first()->auth_code_for_e_kiosks;
            if ($data['authentication_code'] !== $auth_code) {
                return response()->json([
                    'status' => 1,
                    'error' => 'Invalid Credentials',
                    'message' => __("Authentication code is invalid!")
                ], 401);
            }
            $register = IncomingRequest::create($data);
            if ($register) {
                return response()->json([
                    'status' => 0,
                    'message' => "The request has been made!"
                ], 200);
            }
            return response()->json(['error' => __('Something went wrong on the server!')], 422);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login with your device ID and auth code",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"device_id", "authentication_code"},
     *                 @OA\Property(property="device_id", type="string", example="fb5c85e29caf6b73"),
     *                 @OA\Property(property="authentication_code", type="string", example="11002233")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User's current location",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="number", example="0, 1 or 2"),
     *             @OA\Property(property="message", type="string", example="Error message"),
     *             @OA\Property(property="access_token", type="string", example="50|JfWa9kgeeyOrj9LJyLWsA59WECx56Gfk20DD48Z325c29dd")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'device_id' => 'required',
                'authentication_code' => 'required'
            ]);
            $deviceId = $request->device_id;
            $authenticationCode = $request->authentication_code;
            $eKiosk = EKiosk::where(['e_kiosk_id' => $deviceId, 'authentication_code' => $authenticationCode, 'status' => true])->first();
            if ($eKiosk) {
                $token = $eKiosk->createToken($eKiosk->name . '-AuthToken')->plainTextToken;
                return response()->json([
                    "status" => 0,
                    "message" => __("You have successfully logged in!"),
                    'access_token' => $token
                ], 200);
            } else {
                $eKiosk = EKiosk::where(['e_kiosk_id' => $deviceId, 'status' => true])->first();
                if ($eKiosk) {
                    return response()->json([
                        'status' => 1,
                        'message' => __("Authentication code is invalid!")
                    ], 401);
                }
            }

            $checkIncomingRequest = IncomingRequest::where('device_id', $deviceId)->first();
            if ($checkIncomingRequest) {
                return response()->json([
                    "status" => 2,
                    "message" => __("You have requested to register, but your request is still pending!")
                ]);
            }
            return response()->json([
                "status" => 3,
                "message" => "You have not made a request to register"
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 2,
                'errors' => $e->validator->errors()
            ], 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout is not in use in eKiosk",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        $tokenModel = PersonalAccessToken::findToken($token);
        if ($tokenModel) {
            $tokenModel->delete();
            return response()->json(["status" => 200, "message" => "Logged out successfully"]);
        }

        return response()->json(["status" => 404, "message" => "Token not found"], 404);
    }

    /**
     * @OA\Post(
     *     path="/api/logs",
     *     summary="Save logs for the terminal payment process",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"message"},
     *                 @OA\Property(property="message", type="string", example="Error ...")
     *             )
     *         )
     * *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function logs(Request $request)
    {
        try {
            $message = $request->message;
            $date = Carbon::now()->format('Y-m-d');
            $filename = "terminal_logs/e_kiosk_$date.txt";
            $logMessage = $message;
            Storage::disk('local')->append($filename, $logMessage);
            return response()->json(['message' => 'Log saved successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to save log.'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/defaultLanguage",
     *     summary="Get default language",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function defaultLanguage(){
        $defaultLanguage = Language::where('locale', env('DEFAULT_LANGUAGE', 'fr'))->first();
        if (!$defaultLanguage){
            $defaultLanguage = Language::where('locale', 'en')->first();
        }
        return response()->json($defaultLanguage);
    }

    /**
     * @OA\Post(
     *     path="/api/settings/login",
     *     summary="In this endpoint, the pin is given which is needed to enter the settings in the ekioske, and you will receive a status of 200 for the correct pin or 401 for the incorrect pin.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"pin"},
     *                 @OA\Property(property="pin", type="string", example="12231")
     *             )
     *         )
     * *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function loginSettings(Request $request)
    {
        $pin = $request->pin;
        if (auth()->user()->pin_for_settings == $pin) {
            return response()->json(['status' => 200, 'message' => 'Pin is correct.']);
        }
        return response()->json(['status' => 401, 'message' => 'Pin is incorrect.'], 401);
    }

    /**
     * @OA\Post(
     *     path="/api/save/order",
     *     summary="Save an order",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"order_type", "locator", "cart_total_item", "cart_total_price", "payment_method_id", "items", "payment_status", "payment_data"},
     *
     *                 @OA\Property(property="order_type", type="string", example="take_away"),
     *                 @OA\Property(property="locator", type="integer", example=0),
     *                 @OA\Property(property="cart_total_item", type="integer", example=4),
     *                 @OA\Property(property="cart_total_price", type="number", format="float", example=43.8),
     *                 @OA\Property(property="payment_method_id", type="string", format="uuid", example="a8613982-3e41-4e4f-bb98-4ea7d8572495"),
     *
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     minItems=1,
     *                     @OA\Items(
     *                         oneOf={
     *                             @OA\Schema(
     *                                 type="object",
     *                                 required={"type", "id", "price_per", "quantity", "name", "sub_total"},
     *                                 @OA\Property(property="type", type="string", example="product", enum={"product"}),
     *                                 @OA\Property(property="id", type="string", format="uuid", example="42e525cd-93a6-4022-8aea-15289c41a75e"),
     *                                 @OA\Property(property="price_per", type="number", format="float", example=7.9),
     *                                 @OA\Property(property="quantity", type="integer", example=1),
     *                                 @OA\Property(property="name", type="string", example="Chicken Wrap"),
     *                                 @OA\Property(
     *                                     property="size",
     *                                     type="object",
     *                                     required={"small", "medium", "large"},
     *                                     @OA\Property(property="small", type="number", nullable=true),
     *                                     @OA\Property(property="medium", type="number", nullable=true),
     *                                     @OA\Property(property="large", type="number", nullable=true)
     *                                 ),
     *                                 @OA\Property(
     *                                     property="ingredients",
     *                                     type="array",
     *                                     @OA\Items(type="string", format="uuid", example="a11ae51c-31f7-4156-b8c5-8cd33cdb27a6")
     *                                 ),
     *                                 @OA\Property(property="removed_ingredients_names", type="array", @OA\Items(type="string")),
     *                                 @OA\Property(
     *                                     property="modifiers",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         required={"id", "price_per", "name", "quantity"},
     *                                         @OA\Property(property="id", type="string", format="uuid", example="0784fc44-5c99-41ba-8429-099d17bddeec"),
     *                                         @OA\Property(property="price_per", type="number", format="float", example=1),
     *                                         @OA\Property(property="name", type="string", example="Bacon"),
     *                                         @OA\Property(property="quantity", type="integer", example=1)
     *                                     )
     *                                 ),
     *                                 @OA\Property(property="sub_total", type="number", format="float", example=8.9)
     *                             ),
     *                             @OA\Schema(
     *                                 type="object",
     *                                 required={"type", "id", "price_per", "quantity", "name", "sub_total"},
     *                                 @OA\Property(property="type", type="string", example="deal", enum={"deal"}),
     *                                 @OA\Property(property="id", type="string", format="uuid", example="07d0af43-9204-4b77-a228-49469259e478"),
     *                                 @OA\Property(property="price_per", type="number", format="float", example=7.7),
     *                                 @OA\Property(property="quantity", type="integer", example=1),
     *                                 @OA\Property(property="name", type="string", example="Menu maxi box 30x Filets"),
     *                                 @OA\Property(
     *                                     property="products",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         required={"id", "price_per", "name", "quantity", "ingredients", "sub_total"},
     *                                         @OA\Property(property="id", type="string", format="uuid", example="3f04c5ac-0325-4066-8444-2650161ab046"),
     *                                         @OA\Property(property="price_per", type="number", format="float", example=0),
     *                                         @OA\Property(property="name", type="string", example="30x filets mignons"),
     *                                         @OA\Property(property="quantity", type="integer", example=1),
     *                                         @OA\Property(
     *                                             property="ingredients",
     *                                             type="array",
     *                                             @OA\Items(type="string", format="uuid", example="c9a376ea-1fba-4d8d-b9ab-35ff3638dd07")
     *                                         ),
     *                                         @OA\Property(property="sub_total", type="number", format="float", example=0)
     *                                     )
     *                                 ),
     *                                 @OA\Property(property="sub_total", type="number", format="float", example=8.7)
     *                             )
     *                         }
     *                     )
     *                 ),
     *
     *                 @OA\Property(property="payment_status", type="boolean", example=false),
     *                 @OA\Property(
     *                     property="payment_data",
     *                     type="array",
     *                     @OA\Items(type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order saved successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request body"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $items = [];
            $data = $request->all();
            $validator = Validator::make($data, [
                'order_type' => 'required|string|in:take_away,dine_in,delivery',
                'locator' => 'present',
                'cart_total_item' => 'present',
                'cart_total_price' => 'present',
                'payment_method_id' => 'present',
                'items' => 'present|array',
                'items.*.id' => 'required|uuid',
                'items.*.type' => 'present',
                'items.*.price_per' => 'present',
                'items.*.quantity' => 'present',
                // 'items.*.ingredients' => 'present',
                // 'items.*.modifiers' => 'present|array',
                // 'items.*.size' => 'present',
                'items.*.sub_total' => 'present',
                // 'items.*.removed_ingredients_names' => 'present',
                'payment_status' => 'required|boolean'
            ]);

            $validator->after(function ($validator) use ($data) {
                if (isset($data['payment_status']) && $data['payment_status']) {
                    $paymentDataValidator = Validator::make($data, [
                        'payment_data.title' => 'present',
                        'payment_data.type' => 'present',
                        'payment_data.card_type' => 'present',
                        'payment_data.card_number' => 'present',
                        'payment_data.trm_id' => 'present',
                        'payment_data.aid' => 'present',
                        'payment_data.date' => 'present',
                        'payment_data.seq_cnt' => 'present',
                        'payment_data.acq_id' => 'present',
                        'payment_data.total' => 'present',
                    ]);

                    if ($paymentDataValidator->fails()) {
                        $validator->errors()->merge($paymentDataValidator->errors());
                    }
                }
            });

            if ($validator->fails()) {
                DB::rollBack();
                return response()->json(['errors' => $validator->errors()], 422);
            }


            $cardMethodId = config('constants.paymentMethod.paymentMethodCardId');
            $cashMethodId = config('constants.paymentMethod.paymentMethodCashId');
            $type = [];
            $sizeKeys = [
                'small' => __('Small'),
                'medium' => __('Medium'),
                'large' => __('Large')
            ];
            $sizes = [
                "small" => null,
                "medium" => null,
                "large" => null
            ];
            $zReport = ZReport::where([
                'end_z_report' => null,
            ])->latest('created_at')->first();
            $orderReceipt = Sale::withTrashed()->where('is_paid', true)->orderBy('order_receipt', 'desc')->first();
            $user = auth()->user();
            $categoryForKitchen = false;
            $totalPayablePrice = 0;
            $totalDiscount = 0;
            $totalTax = 0;
            $taxSum = [];
            $discountInPercentage = 0;
            $tax = \App\Models\Tax::where('type', $data['order_type'])->first();
            $paidWithCard = $data['payment_method_id'] == $cardMethodId ? true : ($data['payment_method_id'] == $cashMethodId ? false : 'error');
            if ($paidWithCard === 'error') {
                DB::rollBack();
                return response()->json(['status' => 1, 'message' => __('Payment method does not exist')]);
            }
            $calculateCost = $this->getTotalCostAndStockIngredients($data);
            $eachCost = $calculateCost['eachCost'];
            foreach ($data['items'] as $key => $item) {
                $items[$key]['is_ready'] = false;
                $items[$key]['randomKey'] = 100 + $key * 100;
                if ($item['type'] == 'product') {
                    $items[$key]['type'] = 'product';
                    $items[$key]['id'] = $item['id'];
                    $items[$key]['cost'] = isset($eachCost[$item['id']]) ? $eachCost[$item['id']] : 0; //duhet me rregullu pse po vjen [], ne meals
                    $items[$key]['price_per'] = $item['price_per'];
                    $items[$key]['quantity'] = $item['quantity'];
                    $items[$key]['sub_total'] = $item['sub_total'];
                    $product = \App\Models\FoodItem::where('id', $item['id'])->with('ingredients')->first();
                    $items = $this->getItems($product, $items, $key);

                    $taxProd = $product->tax ?? $tax;
                    $taxValue = $taxProd->tax_rate;
                    $priceWithoutTax = $item['sub_total'] / (1 + $taxValue * 0.01);
                    $priceAfterDiscount = $priceWithoutTax - $priceWithoutTax * $discountInPercentage * 0.01;
                    $items[$key]['tax_amount'] = $priceAfterDiscount * $taxValue * 0.01;
                    $items[$key]['discount_amount'] = $priceWithoutTax - $priceAfterDiscount;
                    $items[$key]['tax_id'] = $product->tax?->tax_id ?? $tax->tax_id;
                    $items[$key]['tax_name'] = $product->tax?->name ?? $tax->name;
                    $items[$key]['payable_price'] = $priceAfterDiscount + $priceAfterDiscount * $taxValue * 0.01;
                    $items[$key]['price_neto'] = $priceAfterDiscount;
                    $items[$key]['profit'] = $priceAfterDiscount - (isset($eachCost[$item['id']]) ? $eachCost[$item['id']] : 0); //duhet me rregullu pse po vjen [], ne meals

                    if (isset($taxSum[$taxProd->id])) {
                        $taxSum[$taxProd->id]['tax_amount'] += $items[$key]['tax_amount'];
                    } else {
                        $taxSum[$taxProd->id]['tax_amount'] = $items[$key]['tax_amount'];
                        $taxSum[$taxProd->id]['tax_id'] = $items[$key]['tax_id'];
                        $taxSum[$taxProd->id]['tax_name'] = $items[$key]['tax_name'];
                        $taxSum[$taxProd->id]['tax_rate'] = $taxValue;
                    }
                    $totalPayablePrice += $priceAfterDiscount + $priceAfterDiscount * $taxValue * 0.01;
                    $totalDiscount += $priceWithoutTax - $priceAfterDiscount;
                    $totalTax += $priceAfterDiscount * $taxValue * 0.01;


                    $items[$key]['size'] = $sizes;
                    if (array_key_exists('size', $item)) {
                        $sizeKey = array_keys($item['size'])[0];
                        $items[$key]['size'][$sizeKey] = $item['size'][$sizeKey];

                        $items[$key]['name'] .= ', ' . $sizeKeys[$sizeKey];
                    }


                    if (!$product->category?->category_for_kitchen) {
                        $items[$key]['is_ready'] = true;
                    } else {
                        $categoryForKitchen = true;
                    }
                    $items[$key]['category_for_kitchen'] = $product->category?->category_for_kitchen;
                    $items[$key]['ingredients'] = $item['ingredients'] ?? [];
                    $items[$key]['removed_ingredients_names'] = $item['removed_ingredients_names'] ?? [];
                    $items[$key]['modifiers'] = [];
                    if (isset($item['modifiers']) && is_array($item['modifiers'])) {
                        $items[$key]['modifiers'] = $item['modifiers'];
                    }
                    if (!in_array('product', $type)) {
                        $type[] = 'product';
                    }
                } elseif ($item['type'] == 'meal' || $item['type'] == 'deal') {
                    $meal = \App\Models\Meal::where('id', $item['id'])->with('foodItems', 'foodItems.ingredients')->first();
                    $items[$key]['type'] = 'deal';
                    $items[$key]['id'] = $item['id'];
                    $items[$key]['cost'] = isset($eachCost[$item['id']]) ? $eachCost[$item['id']] : 0; //duhet me rregullu pse po vjen [], ne meals
                    $items[$key]['price_per'] = $item['price_per'];
                    $items[$key]['quantity'] = $item['quantity'];
                    $items[$key]['sub_total'] = $item['sub_total'];
                    $items = $this->getItems($meal, $items, $key);
                    if (!in_array('deal', $type)) {
                        $type[] = 'deal';
                    }
                    $taxProd = $meal->tax ?? $tax;
                    $taxValue = $taxProd->tax_rate;

                    $priceWithoutTax = $item['sub_total'] / (1 + $taxValue * 0.01);
                    $priceAfterDiscount = $priceWithoutTax - $priceWithoutTax * $discountInPercentage * 0.01;
                    $items[$key]['tax_amount'] = $priceAfterDiscount * $taxValue * 0.01;
                    $items[$key]['discount_amount'] = $priceWithoutTax - $priceAfterDiscount;
                    $items[$key]['tax_id'] = $meal->tax?->tax_id ?? $tax->tax_id;
                    $items[$key]['tax_name'] = $product->tax?->name ?? $tax->name;
                    $items[$key]['payable_price'] = $priceAfterDiscount + $priceAfterDiscount * $taxValue * 0.01;
                    $items[$key]['price_neto'] = $priceAfterDiscount;
                    $items[$key]['profit'] = $priceAfterDiscount - (isset($eachCost[$item['id']]) ? $eachCost[$item['id']] : 0); //duhet me rregullu pse po vjen [], ne meals
                    if (isset($taxSum[$taxProd->id])) {
                        $taxSum[$taxProd->id]['tax_amount'] += $items[$key]['tax_amount'];
                    } else {
                        $taxSum[$taxProd->id]['tax_amount'] = $items[$key]['tax_amount'];
                        $taxSum[$taxProd->id]['tax_id'] = $items[$key]['tax_id'];
                        $taxSum[$taxProd->id]['tax_name'] = $items[$key]['tax_name'];
                        $taxSum[$taxProd->id]['tax_rate'] = $taxValue;
                    }

                    $totalPayablePrice += $priceAfterDiscount + $priceAfterDiscount * $taxValue * 0.01;
                    $totalDiscount += $priceWithoutTax - $priceAfterDiscount;
                    $totalTax += $priceAfterDiscount * $taxValue * 0.01;
                    $items[$key]['is_ready'] = true;
                    $items[$key]['products'] = [];
                    $categoryForKitchenMeal = false;
                    foreach ($item['products'] as $key1 => $eachProduct) {
                        $items[$key]['products'][$key1] = $eachProduct;
                        $items[$key]['products'][$key1]['randomKey'] = (100 + $key * 100) + ($key1 + 1);
                        $items[$key]['products'][$key1]['modifiers'] = $eachProduct['modifiers'] ?? [];
                        $product1 = FoodItem::where('id', $eachProduct['id'])->first();

                        $items[$key]['products'][$key1]['is_ready'] = true;
                        if ($product1->category?->category_for_kitchen) {
                            $categoryForKitchen = true;
                            $categoryForKitchenMeal = true;
                            $items[$key]['is_ready'] = false;
                            $items[$key]['products'][$key1]['is_ready'] = false;
                        }
                        $items[$key]['products'][$key1]['id-from-db'] = $product1['id'];
                        $items[$key]['products'][$key1]['category_for_kitchen'] = $product1->category?->category_for_kitchen;
                        $items[$key]['products'][$key1]['name'] = $product1['name'];
                        $items[$key]['products'][$key1]['location_id'] = $product1['location_id'];
                        $items[$key]['products'][$key1]['status'] = $product1['status'];
                        $items[$key]['products'][$key1]['price-from-db'] = $product1['price'];
                        $items[$key]['products'][$key1]['image'] = $product1['image'];
                        $items[$key]['products'][$key1]['food_category_id'] = $product1['food_category_id'];
                        $items[$key]['products'][$key1]['category_name'] = $product1->category?->name;
                        $items[$key]['products'][$key1]['sku'] = $product1['sku'];
                        $items[$key]['products'][$key1]['description'] = $product1['description'];
                        $items[$key]['products'][$key1]['created_at'] = $product1['created_at']->toString();
                        $items[$key]['products'][$key1]['updated_at'] = $product1['updated_at']->toString();

                        $items[$key]['products'][$key1]['size'] = $sizes;

                        if (array_key_exists('size', $eachProduct)) {
                            $sizeKey = array_keys($eachProduct['size'])[0];
                            $items[$key]['products'][$key1]['size'][$sizeKey] = $eachProduct['size'][$sizeKey];

                            $items[$key]['products'][$key1]['name'] .= ', ' . $sizeKeys[$sizeKey];
                        }
                    }
                    $items[$key]['category_for_kitchen'] = $categoryForKitchenMeal;
                }
            }
            foreach ($taxSum as $keyTax => $finalizationTax) {
                $taxSum[$keyTax]['tax_amount'] = round($finalizationTax['tax_amount'], 2);
            }
            $kitchenStatus = $this->currentLocation()->kitchen && $categoryForKitchen;
            if (!$zReport) {
                DB::rollBack();
                return response()->json(['status' => 1, 'message' => __('No point has been opened yet')], 400);
            }
            $orderNumber = Sale::withTrashed()->whereDate('created_at', \Carbon\Carbon::today())
                ->orderBy('order_number', 'desc')
                ->first();

            $staticData = [
                'order_taker_id' => $user->name,
                'z_raport' => [
                    'start_z_report' => $zReport?->start_z_report,
                    'saldo' => $zReport?->saldo,
                    'cash_register_name' => $zReport?->cashRegister?->name,
                    'location' => $zReport?->location?->name
                ],
                'payment_method' => isset($data['payment_method_id']) && $data['payment_method_id'] != 0 ? PaymentMethod::find($data['payment_method_id'])->name : null,
            ];
            $idForResponse = Uuid::uuid4()->toString();
            Sale::create([
                'id' => $idForResponse, // default id(uuid)
                'type' => count($type) == 2 ? 'mixed' : $type[0], // product || deal || mixed
                'order_number' => $orderNumber ? $orderNumber?->order_number + 1 : 1,
                'order_receipt' => $paidWithCard ? ($orderReceipt?->order_receipt + 1) : null,
                'pos_or_kiosk' => 'e_kiosk',
                'z_report_id' => $paidWithCard ? $zReport?->id : null,
                'tracking' => rand(10000, 20000), // order number
                'order_type' => $data['order_type'], // take_away || dine_in ||delivery
                'items' => $items, // all products && deals in a static array
                'tax' => $tax, // tax for dine_in, take_away || delivery
                'sum_taxes' => $taxSum,
                'took_at' => Carbon::now(),
                'order_taker_id' => null,
                'is_preparing' => !$kitchenStatus ? 1 : 0,
                'prepared_at' => !$kitchenStatus ? now() : null,
                'e_kiosk_id' => $user->id,
                'customer_id' => config('constants.role.customerId'),
                'completed_at' => null /*$paidWithCard ? (!$kitchenStatus ? now() : null) : null*/,
                'progress' => !$kitchenStatus ? 100 : 0,
                'chef_id' => null,
                'locator' => $data['locator'] ?? null,
                'payment_method_id' => isset($data['payment_method_id']) && $data['payment_method_id'] != 0 ? $data['payment_method_id'] : null,
                'payment_method_type' => isset($data['payment_method_id']) && $data['payment_method_id'] != 0 ? PaymentMethod::find($data['payment_method_id'])->name : null,
                'biller_id' => null,
                'tax_amount' => $totalTax,
                'is_paid' => (bool)$paidWithCard,
                'cart_total_items' => count($data['items']),
                'cart_total_price' => $data['cart_total_price'], //price without tax and without discount
                'cart_total_cost' => $calculateCost['cost'],
                'profit_after_all' => ($totalPayablePrice - $totalTax) - $calculateCost['cost'], //price - cost
                'payable_after_all' => $totalPayablePrice, //how much the customer pays, including discounts and taxes
                'discount_rate' => 0, // percentage or fixed (value)
                'discount_amount' => 0, // total discount after calculate if value is in percentage
                'is_discount_in_percentage' => true,
                'table_id' => $data['table_id'] ?? null,
                'static_data' => $staticData ?? null,
                'cost_during_preparation' => 0,
                'paid_cash' => 0,
                'paid_bank' => $paidWithCard ? $totalPayablePrice : 0,
                'payment_data' => isset($data['payment_data']) ? $data['payment_data'] : null,
                'payment_status' => $data['payment_status'],
                'payment_return' => 0,
            ]);
            if ($paidWithCard) {
                $this->removeQuantityFromIngredients($calculateCost['allIngredients']);
                $zReport->total_sales += 1;
                $zReport->total_balance_with_card += $totalPayablePrice * 1;
                $zReport->update();
            }

            $orderPrint = Sale::where('id', $idForResponse)->first();
            app()->setLocale(env('DEFAULT_LANGUAGE', 'fr'));
            $inject = new \App\Services\SaleInvoice($orderPrint);
            $printData = $inject->getDataForPrinter();
            $printString = $inject->prepareString($printData);
            app()->setLocale(session()->get('locale') ?? 'fr');
            DB::commit();
            return response()->json(['orderNumber' => $orderPrint->order_number, 'printString' => $printString]);
        } catch (\Exception $e) {

            DB::rollBack();
                return response()->json([
                    'status' => 500,
                    'message' => $e->getTrace(),
                ], 500);
        }
    }

    public function getItems($meal, array $items, int|string $key): array
    {
        $items[$key]['id-from-db'] = $meal['id'];
        $items[$key]['name'] = $meal['name'];
        $items[$key]['location_id'] = $meal['location_id'];
        $items[$key]['status'] = $meal['status'];
        $items[$key]['price-from-db'] = $meal['price'];
        $items[$key]['image'] = $meal['image'];
        $items[$key]['food_category_id'] = $meal['food_category_id'];
        $items[$key]['category_name'] = $meal->category->name;
        $items[$key]['sku'] = $meal['sku'];
        $items[$key]['description'] = $meal['description'];
        $items[$key]['created_at'] = $meal['created_at']->toString();
        $items[$key]['updated_at'] = $meal['updated_at']->toString();
        return $items;
    }

    private function getTotalCostAndStockIngredients($data)
    {
        $allIngredients = [];
        foreach ($data['items'] as $item) {
            $ratio = [
                "Gram" => 1000,
                "Milliliter" => 1000
            ];
            if ($item['type'] == 'product') {
                $productDetails = \App\Models\FoodItem::where('id', $item['id'])->with('ingredients')->first();
                if ($productDetails) {
                    foreach ($productDetails->ingredients as $ingredient) {
                        if ($ingredient->pivot && in_array($ingredient['id'], $item['ingredients'])) {
                            if ($ingredient->pivot->unit == $ingredient->unit || $ingredient->unit == "Unit") {
                                $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$item['quantity'];
                            } else {
                                $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$item['quantity'] * (in_array($ingredient->unit, array_keys($ratio)) ? $ratio[$ingredient->unit] : 0.001);
                            }
                            $allIngredients[] = [
                                'product_id' => $productDetails->id,
                                "ingredient_id" => $ingredient->id,
                                "unit" => $ingredient->pivot->unit,
                                "unit_ingredient" => $ingredient->unit,
                                "quantity" => $ingredient->pivot->quantity * (int)$item['quantity'],
                                "cost" => $cost
                            ];
                        }
                    }
                }
                if (isset($item['modifiers']) && is_array($item['modifiers'])) {
                    foreach ($item['modifiers'] as $modifier) {
                        $modifierDetails = Modifier::where('id', $modifier['id'])->with('ingredients')->first();
                        if ($modifierDetails) {
                            foreach ($modifierDetails->ingredients as $ingredient) {
                                if ($ingredient->pivot->unit == $ingredient->unit || $ingredient->unit == "Unit") {
                                    $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$modifier['quantity'] * (int)$item['quantity'];
                                } else {
                                    $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$modifier['quantity'] * (int)$item['quantity'] * (in_array($ingredient->unit, array_keys($ratio)) ? $ratio[$ingredient->unit] : 0.001);
                                }
                                if ($ingredient->pivot) {
                                    $allIngredients[] = [
                                        'product_id' => $productDetails->id,
                                        "ingredient_id" => $ingredient->id,
                                        "unit" => $ingredient->pivot->unit,
                                        "unit_ingredient" => $ingredient->unit,
                                        "quantity" => $ingredient->pivot->quantity * (int)$modifier['quantity'] * (int)$item['quantity'],
                                        "cost" => $cost,
                                    ];
                                }
                            }
                        }
                    }
                }
            } elseif ($item['type'] == 'meal' || $item['type'] == 'deal') {
                foreach ($item['products'] as $eachProduct) {
                    $productDetails = FoodItem::where('id', $eachProduct['id'])->with('ingredients')->first();
                    if ($productDetails) {
                        foreach ($productDetails->ingredients as $ingredient) {
                            if ($ingredient->pivot && in_array($ingredient['id'], $eachProduct['ingredients'])) {
                                if ($ingredient->pivot->unit == $ingredient->unit || $ingredient->unit == "Unit") {
                                    $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$eachProduct['quantity'] * (int)$item['quantity'];
                                } else {
                                    $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$eachProduct['quantity'] * (int)$item['quantity'] * (in_array($ingredient->unit, array_keys($ratio)) ? $ratio[$ingredient->unit] : 0.001);
                                }
                                $allIngredients[] = [
                                    'deal_id' => $item['id'],
                                    "ingredient_id" => $ingredient->id,
                                    "unit_ingredient" => $ingredient->unit,
                                    "unit" => $ingredient->pivot->unit,
                                    "quantity" => $ingredient->pivot->quantity * (int)$eachProduct['quantity'] * $item['quantity'],
                                    "cost" => $cost
                                ];
                            }
                        }
                    }
                    if (isset($eachProduct['modifiers']) && is_array($eachProduct['modifiers'])) {
                        foreach ($eachProduct['modifiers'] as $modifier) {
                            $modifierDetails = Modifier::where('id', $modifier['id'])->with('ingredients')->first();
                            if ($modifierDetails) {
                                foreach ($modifierDetails->ingredients as $ingredient) {
                                    if ($ingredient->pivot) {
                                        if ($ingredient->pivot->unit == $ingredient->unit || $ingredient->unit == "Unit") {
                                            $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$eachProduct['quantity'] * (int)$item['quantity'];
                                        } else {
                                            $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$eachProduct['quantity'] * (int)$item['quantity'] * (in_array($ingredient->unit, array_keys($ratio)) ? $ratio[$ingredient->unit] : 0.001);
                                        }
                                        $allIngredients[] = [
                                            'deal_id' => $item['id'],
                                            "ingredient_id" => $ingredient->id,
                                            "unit" => $ingredient->pivot->unit,
                                            "unit_ingredient" => $ingredient->unit,
                                            "quantity" => $ingredient->pivot->quantity * (int)$modifier['quantity'] * (int)$eachProduct['quantity'] * $item['quantity'],
                                            "cost" => $cost
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $aggregatedCostsById = [];
        foreach ($allIngredients as $ingredient) {
            $idKey = isset($ingredient['deal_id']) ? 'deal_id' : 'product_id';
            $id = $ingredient[$idKey];
            if (!isset($aggregatedCostsById[$id])) {
                $aggregatedCostsById[$id] = 0;
            }
            $aggregatedCostsById[$id] += $ingredient['cost'];
        }
        $totalCost = array_reduce($allIngredients, function ($carry, $item) {
            $cost = isset($item['cost']) && is_numeric($item['cost']) ? $item['cost'] : 0;
            return $carry + $cost;
        }, 0);

        return [
            'cost' => $totalCost,
            'eachCost' => $aggregatedCostsById,
            'allIngredients' => $allIngredients
        ];
    }

    private function removeQuantityFromIngredients($allIngredients): void
    {
        $conversionRatios = [
            'Liter' => [
                'Milliliter' => 0.001
            ],
            'Milliliter' => [
                'Liter' => 1000,
            ],
            'Kilogram' => [
                'Gram' => 0.001,
            ],
            'Gram' => [
                'Kilogram' => 1000,
            ],
        ];
        $aggregatedQuantities = [];
        foreach ($allIngredients as $ingredient) {
            $ingredientId = $ingredient["ingredient_id"];
            $quantity = $ingredient["quantity"];
            $unit = $ingredient["unit"];
            $unitIngredient = $ingredient["unit_ingredient"];

            if ($unit !== $unitIngredient && isset($conversionRatios[$unitIngredient][$unit])) {
                $quantity *= $conversionRatios[$unitIngredient][$unit];
            }

            if (!isset($aggregatedQuantities[$ingredientId])) {
                $aggregatedQuantities[$ingredientId] = [
                    'quantity' => 0,
                    'unit' => $unitIngredient,
                ];
            }
            $aggregatedQuantities[$ingredientId]['quantity'] += $quantity;
        }
        foreach ($aggregatedQuantities as $ingredientId => $ing) {
            $removeQty = Ingredient::find($ingredientId);
            $removeQty->quantity -= $ing['quantity'];
            $removeQty->update();
        }
    }
}
