$validator = Illuminate\Support\Facades\Validator::make(
    ['password' => null, 'password_confirmation' => null],
    ['password' => 'nullable|string|min:8|confirmed']
);

if ($validator->fails()) {
    echo "Fails: \n";
    print_r($validator->errors()->toArray());
} else {
    echo "Passes validate!\n";
}
