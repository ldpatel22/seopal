
class RegisterController extends Controller
{
    public function register(Request $request): JsonResponse
{
    // Validate the request data
    $request->validate([
                           'name' => 'required|string|max:255',
                           'email' => 'required|string|email|max:255|unique:users',
                           'password' => 'required|string|min:8|confirmed',
                       ]);

    // Create the user in the database
    $user = User::create([
        'name' => $request->name,
    'title' => $request->title,
    'email' => $request->email,
    'password' => Hash::make($request->password),
]);

    return response()->json([
                                'message' => 'Registration successful',
                                'redirect' => route('login'),
                            ]);
}
}
