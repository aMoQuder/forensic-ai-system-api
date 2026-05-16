<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContactController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/contacts",
     *     tags={"Contacts"},
     *     summary="Get all contacts",
     *     description="Retrieve a list of all contacts",
     *     @OA\Response(
     *         response=200,
     *         description="Contacts retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Contacts retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Contact")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'msg' => 'Contacts retrieved successfully',
            'data' => ContactResource::collection(Contact::all()),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/contacts",
     *     tags={"Contacts"},
     *     summary="Create a new contact",
     *     description="Store a new contact message",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name","email","message"},
     *             @OA\Property(property="name", type="string", example="Ahmed Mohamed"),
     *             @OA\Property(property="email", type="string", example="ahmed@example.com"),
     *             @OA\Property(property="message", type="string", example="Hello, I want to contact you.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contact created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Contact")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = validator($request->all(), [
            'name' => 'required|string|min:3',
            'email' => 'required|email',
            'message' => 'required|min:7|max:2000',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'success' => false,
                'msg' => 'Validation error',
                'errors' => $validated->errors()
            ], 422);
        }

        $contact = Contact::create($request->only(['name', 'email', 'message']));

        return response()->json([
            'success' => true,
            'msg' => 'Contact created successfully',
            'data' => new ContactResource($contact),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/contacts/{id}",
     *     tags={"Contacts"},
     *     summary="Get a single contact",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the contact",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Contact")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Contact not found")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'success' => false,
                'msg' => 'Contact not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'msg' => 'Contact retrieved successfully',
            'data' => new ContactResource($contact),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/contacts/{id}",
     *     tags={"Contacts"},
     *     summary="Delete a contact",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the contact",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Contact deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Contact not found")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'success' => false,
                'msg' => 'Contact not found'
            ], 404);
        }

        $contact->delete();

        return response()->json([
            'success' => true,
            'msg' => 'Contact deleted successfully'
        ]);
    }
}
