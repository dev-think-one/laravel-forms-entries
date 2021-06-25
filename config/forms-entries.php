<?php

return [
    'tables' => [
        'forms-entries' => 'forms-entries',
    ],

    'defaults' => [
        'is_need_saving'       => env('FORMS_ENTRIES_NEED_SAVING_TO_STORAGE', true),
        'is_need_notification' => env('FORMS_ENTRIES_NEED_NOTIFICATIONS', true),
        'content_class'        => env('FORMS_ENTRIES_CONTENT_CLASS', \FormEntries\CastsData\JsonData\FormEntryContentJson::class),
        'storage_model_class'  => env('FORMS_ENTRIES_STORAGE_MODEL_CLASS', \FormEntries\Models\FormEntry::class),
    ],
];
