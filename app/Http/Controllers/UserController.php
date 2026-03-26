<?php

// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class UserController extends Controller
// {
//     // List all users (Admin only)
//     public function index()
//     {
//         $users = User::all(['id', 'name', 'email', 'role', 'created_at']);
//         return response()->json($users, 200);
//     }

//     // Update user role (Admin only)
//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'role' => 'required|integer|in:0,1'
//         ]);

//         $user = User::findOrFail($id);
//         $user->role = $request->role;
//         $user->save();

//         return response()->json([
//             'message' => 'User role updated successfully',
//             'user' => $user
//         ], 200);
//     }

//     // Delete a user (Admin only)
//     public function destroy($id)
//     {
//         $user = User::findOrFail($id);
//         $user->delete();

//         return response()->json(['message' => 'User deleted successfully'], 200);
//     }

//     public function paid(Request $request)
// {
//     $user = auth()->user();

//     $data = [
//         'user_id' => $user->id,
//         'name' => $request->name,
//         'email' => $request->email,
//         'address' => $request->address,
//         'city' => $request->city,
//         'postal_code' => $request->postalCode,
//         'country' => $request->country,
//         'cart' => json_encode($request->cart),
//         'total' => $request->total,
//     ];

//     DB::table('paid_users')->insert($data);

//     return response()->json(['message' => 'Payment recorded successfully']);
// }

// public function getPaidUsers()
// {
//     $users = DB::table('paid_users')->latest()->get();
//     return response()->json($users);
// }


// }


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display all users (Admin only)
     */
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')->get();

        return response()->json($users, 200);
    }

    /**
     * Update user role (Admin only)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|integer|in:0,1',
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Delete a user (Admin only)
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Store payment info after checkout
     */
   public function paid(Request $request)
{
    try {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string',
            'city' => 'required|string',
            'postalCode' => 'required|string',
            'country' => 'required|string',
            'cart' => 'required|array',
            'total' => 'required|numeric',
        ]);

        DB::table('paid_users')->insert([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'postal_code' => $validated['postalCode'],
            'country' => $validated['country'],
            'cart' => json_encode($validated['cart']),
            'total' => $validated['total'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Payment recorded successfully'], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error saving payment',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Retrieve all paid users (Admin only)
     */
    public function getPaidUsers()
    {
        $paidUsers = DB::table('paid_users')->latest()->get();

        return response()->json($paidUsers);
    }
}
