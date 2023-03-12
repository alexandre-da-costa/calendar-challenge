<?php

return [
    // Base URL for the UserGems API
    'api_base_url' => env(
        'USERGEMS_API_BASE_URL',
        'https://app.usergems.com/api'
    ),

    // Calendar API configuration
    'calendar' => [
        'endpoint' => env(
            'USERGEMS_CALENDAR_ENDPOINT',
            'hiring/calendar-challenge/events'
        ),
        'api_keys_table' => env(
            'USERGEMS_CALENDAR_API_KEYS_TABLE',
            'calendar_api_keys'
        ),
        'owner' => [
            'model_class_fqn' => env(
                'USERGEMS_SALES_REPRESENTATIVES_MODEL',
                \App\Models\SalesRepresentative::class
            ),
            'id_column_name' => env(
                'USERGEMS_SALES_REPRESENTATIVES_MODEL',
                'sales_representative_id'
            ),
        ],
    ],

    // Person data API configuration
    'person_data' => [
        'api_key' => env(
            'USERGEMS_PERSON_DATA_API_KEY',
            '9d^XItOjTAGSG5ch'
        ),
        'endpoint' => env(
            'USERGEMS_PERSON_DATA_ENDPOINT',
            '/hiring/calendar-challenge/person/'
        ),
    ],
];
