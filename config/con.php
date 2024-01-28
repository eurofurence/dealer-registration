<?php

use Carbon\Carbon;

return [
    'timezone' => env('CON_TIMEZONE'),
    'reg_start_date' => Carbon::createFromIsoFormat('YYYY-MM-DDTHH:mm:ss', env('CON_REG_START_DATETIME_ISO', '1970-01-01T00:00:00'), env('CON_TIMEZONE')),
    'reg_end_date' => Carbon::createFromIsoFormat('YYYY-MM-DDTHH:mm:ss', env('CON_REG_END_DATETIME_ISO', '1970-01-01T12:00:00'), env('CON_TIMEZONE')),
    'assistant_end_date' => Carbon::createFromIsoFormat('YYYY-MM-DDTHH:mm:ss', env('CON_ASSISTANT_END_DATETIME_ISO', '1970-01-01T20:00:00'), env('CON_TIMEZONE')),
    'con_end_date' => Carbon::createFromIsoFormat('YYYY-MM-DDTHH:mm:ss', env('CON_END_DATETIME_ISO', '1970-01-01T23:59:59'), env('CON_TIMEZONE')),
    'dealers_tos_url' => env('CON_DEALERS_TOS_URL'),
    'idp_url' => env('CON_IDP_URL'),
    'dealers_email' => env('CON_DEALERS_EMAIL'),
    'con_name' => env('CON_NAME'),
    'con_name_short' => env('CON_NAME_SHORT'),
    'payment_timeframe' => env('CON_PAYMENT_TIMEFRAME'),
    'admin_group' => env('CON_IDP_GROUP_ADMIN'),
    'frontdesk_group' => env('CON_IDP_GROUP_FRONTDESK'),
    'day_1_name' => env('CON_DAY_1', 'Wednesday'),
    'day_2_name' => env('CON_DAY_2', 'Thursday'),
    'day_3_name' => env('CON_DAY_3', 'Friday'),
    'day_4_name' => env('CON_DAY_4', 'Saturday'),
];
