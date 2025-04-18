<?php

return [
    'avatar_column' => 'avatar_url',
    'disk' => env('FILESYSTEM_DISK', 'public'),
    'visibility' => 'public',
    'show_custom_fields' => true,
    'custom_fields' => [
        'number_cellphone' => [
            'type' => 'text',
            'label' => 'Número de Celular',
            'placeholder' => 'Ingresa tu número de celular',
            'required' => false,
            'rules' => ['nullable', 'regex:/^[0-9]+$/', 'max:20'],
            'column_span' => 'full',
            'attributes' => [
                'inputmode' => 'numeric',
                'pattern' => '[0-9]*',
                'title' => 'Solo se permiten números',
            ],
        ],
        'number_phone' => [
            'type' => 'text',
            'label' => 'Número de Teléfono',
            'placeholder' => 'Ingresa tu número de teléfono',
            'required' => false,
            'rules' => ['nullable', 'regex:/^[0-9]+$/', 'max:20'],
            'column_span' => 'full',
            'attributes' => [
                'inputmode' => 'numeric',
                'pattern' => '[0-9]*',
                'title' => 'Solo se permiten números',
            ],
        ],

        'neighborhood' => [
            'type' => 'text',
            'label' => 'Barrio',
            'placeholder' => 'Ingresa tu barrio',
            'required' => false,
            'rules' => ['nullable', 'string', 'max:255'],
            'column_span' => 'full',
        ],
        'address' => [
            'type' => 'text',
            'label' => 'Dirección',
            'placeholder' => 'Ingresa tu dirección',
            'required' => false,
            'rules' => ['nullable', 'string', 'max:255'],
            'column_span' => 'full',
        ],
    ],
];
