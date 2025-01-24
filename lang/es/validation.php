<?php

return [
    'name' => [
        'required' => 'El campo nombre es obligatorio.',
        'string' => 'El nombre debe ser una cadena de texto.',
        'max' => 'El nombre no puede tener más de :max caracteres.',
    ],
    'email' => [
        'required' => 'El campo correo electrónico es obligatorio.',
        'email' => 'Por favor, introduce una dirección de correo válida.',
        'unique' => 'Este correo electrónico ya está registrado.',
    ],
    // ... rest of translations
]; 