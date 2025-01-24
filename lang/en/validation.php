<?php

return [
    'name' => [
        'required' => 'The name field is required.',
        'string' => 'The name must be a text string.',
        'max' => 'The name cannot be longer than :max characters.',
    ],
    'email' => [
        'required' => 'The email field is required.',
        'email' => 'Please provide a valid email address.',
        'unique' => 'This email is already registered.',
    ],
    'password' => [
        'required' => 'The password field is required.',
        'string' => 'The password must be a text string.',
        'min' => 'The password must be at least :min characters.',
        'confirmed' => 'The password confirmation does not match.',
    ],
]; 