<?php

return [

    'tables' => [
        'forms-entries' => env('FORMS_ENTRIES_TABLE', 'forms_entries'),
    ],

    'routing' => [
        'path'                => env('FORMS_ENTRIES_ROUTE_PATH', 'forms-entries'),
        'form_name_parameter' => env('FORMS_ENTRIES_FORM_NAME_PARAMETER', 'form_type'),
    ],

    'defaults' => [
        'auth_guard'             => null,
        'should_store'           => env('FORMS_ENTRIES_SHOULD_STORE', true),
        'should_notify'          => env('FORMS_ENTRIES_SHOULD_NOTIFY', true),
        'content_class'          => env('FORMS_ENTRIES_CONTENT_CLASS', \FormEntries\Forms\UniversalFormContent::class),
        'storage_model_class'    => env('FORMS_ENTRIES_STORAGE_MODEL_CLASS', \FormEntries\Models\FormEntry::class),
        'notification_class'     => env('FORMS_ENTRIES_NOTIFICATION_CLASS', \FormEntries\Notifications\FormEntryReceived::class),
        'notification_receivers' => [
            'email' => [
                // 'email@test.com' => 'Test User',
            ],
        ],
    ],

];
