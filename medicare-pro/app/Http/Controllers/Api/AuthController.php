<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     title="MediCare Pro API",
 *     description="نظام إدارة المستشفيات والعيادات الشامل | Comprehensive Hospital & Clinic Management System API. Supports Arabic (default) and English languages via Accept-Language header.",
 *     version="1.0.0",
 *     @OA\Contact(
 *         email="support@medicare-pro.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8080/api",
 *     description="Local Development Server | خادم التطوير المحلي"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Laravel Sanctum token authentication | مصادقة رمز Laravel Sanctum"
 * )
 */
class AuthController extends Controller
{
    /**
     * Register a new patient account
     * تسجيل حساب مريض جديد
     *
     * @OA\Post(
     *     path="/v1/auth/register",
     *     tags={"Authentication"},
     *     summary="Register new patient | تسجيل مريض جديد",
     *     description="Creates a new patient account with the provided details. Returns authentication token on success. Returns user data and Bearer token.
     * ينشئ حساب مريض جديد بالبيانات المقدمة. يُرجع رمز المصادقة عند النجاح.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name","email","phone","password","password_confirmation"},
     *                 @OA\Property(property="name", type="string", maxLength=255, example="أحمد محمد", description="Full name | الاسم الكامل"),
     *                 @OA\Property(property="email", type="string", format="email", example="ahmed@example.com", description="Email address | البريد الإلكتروني"),
     *                 @OA\Property(property="phone", type="string", maxLength=20, example="+966501234567", description="Phone number | رقم الهاتف"),
     *                 @OA\Property(property="password", type="string", minLength=8, example="SecurePass123", description="Password (min 8 chars) | كلمة المرور"),
     *                 @OA\Property(property="password_confirmation", type="string", example="SecurePass123", description="Password confirmation | تأكيد كلمة المرور"),
     *                 @OA\Property(property="hospital_id", type="integer", nullable=true, example=1, description="Hospital ID | معرف المستشفى"),
     *                 @OA\Property(property="gender", type="string", enum={"male","female","other"}, example="male", description="Gender | الجنس"),
     *                 @OA\Property(property="date_of_birth", type="string", format="date", example="1990-05-15", description="Date of birth | تاريخ الميلاد"),
     *                 @OA\Property(property="blood_type", type="string", enum={"A+","A-","B+","B-","AB+","AB-","O+","O-"}, example="O+", description="Blood type | فصيلة الدم")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful | تم التسجيل بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registration successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     ref="#/components/schemas/UserResource"
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abc123..."),
     *                 @OA\Property(property="token_type", type="string", example="Bearer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error | خطأ في التحقق",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'hospital_id' => 'nullable|exists:hospitals,id',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        ]);

        return DB::transaction(function () use ($validated) {
            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'patient',
                'status' => 'active',
                'language_preference' => $validated['hospital_id']
                    ? Hospital::find($validated['hospital_id'])->default_language ?? 'ar'
                    : 'ar',
                'hospital_id' => $validated['hospital_id'] ?? null,
            ]);

            Patient::create([
                'user_id' => $user->id,
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'blood_type' => $validated['blood_type'] ?? null,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => __('auth.registration_success'),
                'data' => [
                    'user' => new UserResource($user->load('patient')),
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 201);
        });
    }

    /**
     * Login user and get authentication token
     * تسجيل الدخول والحصول على رمز المصادقة
     *
     * @OA\Post(
     *     path="/v1/auth/login",
     *     tags={"Authentication"},
     *     summary="Login user | تسجيل الدخول",
     *     description="Authenticates user with email and password. Returns Bearer token on success. Supports all user roles (patient, doctor, receptionist, nurse, pharmacist, hospital_admin, super_admin).
     * يصادق على المستخدم بالبريد الإلكتروني وكلمة المرور. يُرجع رمز Bearer عند النجاح.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email","password"},
     *                 @OA\Property(property="email", type="string", format="email", example="ahmed@example.com", description="User email | البريد الإلكتروني"),
     *                 @OA\Property(property="password", type="string", example="password123", description="User password | كلمة المرور")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful | تم تسجيل الدخول بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/UserResource"),
     *                 @OA\Property(property="token", type="string", example="1|abc123..."),
     *                 @OA\Property(property="token_type", type="string", example="Bearer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid credentials | بيانات غير صحيحة",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object", @OA\Property(property="email", type="array", @OA\Items(type="string")))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Account suspended | الحساب معلق",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Account suspended")
     *         )
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => [__('auth.invalid_credentials')],
            ]);
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return response()->json([
                'message' => __('auth.account_suspended'),
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'model_type' => \App\Models\User::class,
            'model_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $userData = $user;
        if ($user->isDoctor()) {
            $userData->load('doctor.department');
        } elseif ($user->isPatient()) {
            $userData->load('patient');
        }

        return response()->json([
            'message' => __('auth.login_success'),
            'data' => [
                'user' => new UserResource($userData),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Logout user and invalidate current token
     * تسجيل الخروج وإلغاء الرمز الحالي
     *
     * @OA\Post(
     *     path="/v1/auth/logout",
     *     tags={"Authentication"},
     *     summary="Logout user | تسجيل الخروج",
     *     description="Invalidates the current authentication token. User must re-authenticate to access protected endpoints.
     * يُلغي رمز المصادقة الحالي. يجب على المستخدم إعادة المصادقة للوصول للنقاط المحمية.",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful | تم تسجيل الخروج بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated | غير مصادق")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'logout',
            'model_type' => \App\Models\User::class,
            'model_id' => $request->user()->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => __('auth.logout_success'),
        ]);
    }

    /**
     * Get the authenticated user's profile
     * الحصول على بيانات المستخدم المصادق عليه
     *
     * @OA\Get(
     *     path="/v1/auth/me",
     *     tags={"Authentication"},
     *     summary="Get current user profile | عرض بيانات المستخدم الحالي",
     *     description="Returns the profile data of the currently authenticated user. Includes role-specific data (doctor department, patient info).
     * يُرجع بيانات الملف الشخصي للمستخدم المصادق عليه حالياً. يتضمن بيانات حسب الدور.",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile data | بيانات الملف الشخصي",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/UserResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated | غير مصادق")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->isDoctor()) {
            $user->load('doctor.department');
        } elseif ($user->isPatient()) {
            $user->load('patient');
        }
        return response()->json([
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Refresh the authentication token
     * تحديث رمز المصادقة
     *
     * @OA\Post(
     *     path="/v1/auth/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh authentication token | تحديث رمز المصادقة",
     *     description="Invalidates the current token and issues a new one. Useful for security rotation.
     * يُلغي الرمز الحالي ويُصدر رمزاً جديداً.",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed | تم تحديث الرمز",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Token refreshed"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="1|newabc123..."),
     *                 @OA\Property(property="token_type", type="string", example="Bearer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated | غير مصادق")
     * )
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        $token = $request->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => __('auth.token_refreshed'),
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Request a password reset link
     * طلب رابط إعادة تعيين كلمة المرور
     *
     * @OA\Post(
     *     path="/v1/auth/forgot-password",
     *     tags={"Authentication"},
     *     summary="Request password reset | طلب إعادة تعيين كلمة المرور",
     *     description="Sends a password reset link to the user's registered email address.
     * يُرسل رابط إعادة تعيين كلمة المرور إلى البريد الإلكتروني المسجل.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email"},
     *                 @OA\Property(property="email", type="string", format="email", example="user@example.com", description="Registered email | البريد المسجل")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reset link sent | تم إرسال رابط إعادة التعيين",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password reset link sent")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error | خطأ في التحقق")
     * )
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => __('auth.password_reset_sent'),
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [__('messages.error')],
        ]);
    }

    /**
     * Reset password with token
     * إعادة تعيين كلمة المرور بالرمز
     *
     * @OA\Post(
     *     path="/v1/auth/reset-password",
     *     tags={"Authentication"},
     *     summary="Reset password | إعادة تعيين كلمة المرور",
     *     description="Resets the user's password using the token received via email.
     * يُعيد تعيين كلمة مرور المستخدم باستخدام الرمز المُستلم عبر البريد الإلكتروني.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"token","email","password","password_confirmation"},
     *                 @OA\Property(property="token", type="string", description="Reset token | رمز إعادة التعيين"),
     *                 @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *                 @OA\Property(property="password", type="string", minLength=8, example="NewPass123"),
     *                 @OA\Property(property="password_confirmation", type="string", example="NewPass123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successful | تم إعادة تعيين كلمة المرور بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password reset successfully")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error | خطأ في التحقق")
     * )
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => __('auth.password_reset_success'),
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [__('messages.error')],
        ]);
    }

    /**
     * Change user language preference
     * تغيير اللغة المفضلة للمستخدم
     *
     * @OA\Post(
     *     path="/v1/auth/change-language",
     *     tags={"Authentication"},
     *     summary="Change language preference | تغيير اللغة المفضلة",
     *     description="Updates the authenticated user's language preference. Supported values: ar (Arabic), en (English).
     * يُحدّث اللغة المفضلة للمستخدم. القيم المدعومة: ar (العربية)، en (الإنجليزية).",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"language"},
     *                 @OA\Property(property="language", type="string", enum={"ar","en"}, example="en", description="Language code | رمز اللغة")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Language updated | تم تحديث اللغة",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="language", type="string", example="en")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated | غير مصادق"),
     *     @OA\Response(response=422, description="Invalid language value | قيمة لغة غير صالحة")
     * )
     */
    public function changeLanguage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language' => 'required|in:ar,en',
        ]);

        $request->user()->update([
            'language_preference' => $validated['language'],
        ]);

        return response()->json([
            'message' => __('auth.language_changed'),
            'data' => [
                'language' => $validated['language'],
            ],
        ]);
    }
}
