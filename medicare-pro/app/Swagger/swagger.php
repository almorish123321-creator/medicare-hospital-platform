<?php
/**
 * @OA\Info(
 *     title="MediCare Pro API",
 *     description="Comprehensive Hospital & Clinic Management System API. Supports Arabic (default) and English languages via Accept-Language header.",
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
 *     description="Local Development Server"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="User registration, login, and token management"
 * )
 *
 * @OA\Tag(
 *     name="Patient",
 *     description="Patient-facing endpoints (mobile app)"
 * )
 *
 * @OA\Tag(
 *     name="Doctor",
 *     description="Doctor dashboard and consultation endpoints"
 * )
 *
 * @OA\Tag(
 *     name="Receptionist",
 *     description="Receptionist queue and appointment management"
 * )
 *
 * @OA\Tag(
 *     name="Nurse",
 *     description="Nurse vital signs recording"
 * )
 *
 * @OA\Tag(
 *     name="Pharmacist",
 *     description="Pharmacist prescription and medication management"
 * )
 *
 * @OA\Tag(
 *     name="Hospital Admin",
 *     description="Hospital administration endpoints"
 * )
 *
 * @OA\Tag(
 *     name="Super Admin",
 *     description="System-wide administration endpoints"
 * )
 *
 * @OA\Tag(
 *     name="Public",
 *     description="Publicly accessible endpoints (no auth required)"
 * )
 */

/**
 * @OA\Get(
 *     path="/v1/languages",
 *     tags={"Public"},
 *     summary="Get supported languages",
 *     description="Returns a list of supported languages",
 *     @OA\Response(response=200, description="Successful operation")
 * )
 */

/**
 * @OA\Post(
 *     path="/v1/auth/register",
 *     tags={"Authentication"},
 *     summary="Register new patient",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"name","email","phone","password","password_confirmation"},
 *                 @OA\Property(property="name", type="string", maxLength=255),
 *                 @OA\Property(property="email", type="string", format="email"),
 *                 @OA\Property(property="phone", type="string", maxLength=20),
 *                 @OA\Property(property="password", type="string", minLength=8),
 *                 @OA\Property(property="password_confirmation", type="string"),
 *                 @OA\Property(property="hospital_id", type="integer", nullable=true),
 *                 @OA\Property(property="gender", type="string", enum={"male","female","other"}),
 *                 @OA\Property(property="date_of_birth", type="string", format="date"),
 *                 @OA\Property(property="blood_type", type="string", enum={"A+","A-","B+","B-","AB+","AB-","O+","O-"})
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Registration successful"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */

/**
 * @OA\Post(
 *     path="/v1/auth/login",
 *     tags={"Authentication"},
 *     summary="Login user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"email","password"},
 *                 @OA\Property(property="email", type="string", format="email"),
 *                 @OA\Property(property="password", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Login successful"),
 *     @OA\Response(response=401, description="Invalid credentials")
 * )
 */

/**
 * @OA\Post(
 *     path="/v1/auth/logout",
 *     tags={"Authentication"},
 *     summary="Logout user",
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="Logout successful")
 * )
 */

/**
 * @OA\Get(
 *     path="/v1/auth/me",
 *     tags={"Authentication"},
 *     summary="Get current user",
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="User data"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */

/**
 * @OA\Get(
 *     path="/v1/hospitals",
 *     tags={"Public"},
 *     summary="List all hospitals",
 *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
 *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
 *     @OA\Response(response=200, description="List of hospitals")
 * )
 */

/**
 * @OA\Post(
 *     path="/v1/patient/appointments",
 *     tags={"Patient"},
 *     summary="Book new appointment",
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"doctor_id","hospital_id","department_id","appointment_date","appointment_time"},
 *                 @OA\Property(property="doctor_id", type="integer"),
 *                 @OA\Property(property="hospital_id", type="integer"),
 *                 @OA\Property(property="department_id", type="integer"),
 *                 @OA\Property(property="appointment_date", type="string", format="date"),
 *                 @OA\Property(property="appointment_time", type="string", format="time"),
 *                 @OA\Property(property="symptoms", type="string", nullable=true),
 *                 @OA\Property(property="notes", type="string", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Appointment booked"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */

/**
 * @OA\Get(
 *     path="/v1/patient/queue-status",
 *     tags={"Patient"},
 *     summary="Get current queue status",
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="Queue status data")
 * )
 */

/**
 * @OA\Put(
 *     path="/v1/doctor/appointments/{id}/start",
 *     tags={"Doctor"},
 *     summary="Start consultation",
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Consultation started"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */

/**
 * @OA\Post(
 *     path="/v1/doctor/medical-records",
 *     tags={"Doctor"},
 *     summary="Create medical record",
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"appointment_id"},
 *                 @OA\Property(property="appointment_id", type="integer"),
 *                 @OA\Property(property="vital_signs", type="object"),
 *                 @OA\Property(property="symptoms", type="string"),
 *                 @OA\Property(property="diagnosis", type="string"),
 *                 @OA\Property(property="notes", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Medical record created")
 * )
 */

/**
 * @OA\Post(
 *     path="/v1/receptionist/walk-in",
 *     tags={"Receptionist"},
 *     summary="Register walk-in patient",
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"patient_id","doctor_id","department_id"},
 *                 @OA\Property(property="patient_id", type="integer"),
 *                 @OA\Property(property="doctor_id", type="integer"),
 *                 @OA\Property(property="department_id", type="integer"),
 *                 @OA\Property(property="symptoms", type="string", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Walk-in registered")
 * )
 */

/**
 * @OA\Get(
 *     path="/v1/admin/dashboard",
 *     tags={"Hospital Admin"},
 *     summary="Get hospital dashboard stats",
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="Dashboard statistics")
 * )
 */

/**
 * @OA\Get(
 *     path="/v1/super-admin/hospitals",
 *     tags={"Super Admin"},
 *     summary="List all hospitals",
 *     security={{"sanctum":{}}},
 *     @OA\Response(response=200, description="List of hospitals")
 * )
 */

/**
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Laravel Sanctum token authentication"
 * )
 */

/**
 * @OA\Parameter(
 *     name="Accept-Language",
 *     in="header",
 *     description="Language preference (ar or en)",
 *     @OA\Schema(type="string", enum={"ar","en"}, default="ar")
 * )
 */
