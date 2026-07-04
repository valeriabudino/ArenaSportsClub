<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'El campo :attribute debe ser un email válido.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'max' => [
        'string' => 'El campo :attribute no debe superar los :max caracteres.',
    ],
    'unique' => 'El :attribute ya está registrado.',

    'attributes' => [
        'name' => 'nombre',
        'email' => 'email',
        'phone' => 'WhatsApp',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
        'form.email' => 'email',
        'form.password' => 'contraseña',
    ],
];