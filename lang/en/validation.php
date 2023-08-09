<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'merchandise' => [
            'required_unless' => 'Please add a description of the merchandise you plan to offer.'
        ],
        'denType' => [
            'required_if' => 'Please select a location.'
        ],
        'space' => [
            'required_if' => 'Please select a table size when applying as :value.'
        ],
        'tos' => [
            'required' => 'Please agree to our Terms of Service.'
        ],
        'twitter' => [
            'regex' => 'Please verify your Twitter handle, it must be between 4 and 15 characters and only contain letters, numbers and underscores (_).'
        ],
        'telegram' => [
            'regex' => 'Please verify your Telegram handle, it must be between 5 and 32 characters and only contain letters, numbers and underscores (_).'
        ],
        'discord' => [
            'regex' => 'Please verify your Discord handle, it must be between 2 and 32 characters.'
        ],
        'attends_sat' => [
            'required_without_all' => 'Please select at least one day when you will be in the Dealers\' Den.'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
    ],

];
