<?php

use Carbon\Carbon;

return [
    'reg_end_date' => Carbon::createFromIsoFormat('YYYY-MM-DDTHH:mm:ss', env('CON_REG_END_DATETIME_ISO'), env('CON_TIMEZONE')),
    'assistant_end_date' => Carbon::createFromIsoFormat('YYYY-MM-DDTHH:mm:ss', env('CON_ASSISTANT_END_DATETIME_ISO'), env('CON_TIMEZONE')),
    'dealers_tos_url' => env('CON_DEALERS_TOS_URL'),
    'idp_url' => env('CON_IDP_URL'),
    'dealers_email' => env('CON_DEALERS_EMAIL'),
    'con_name' => env('CON_NAME'),
    'con_name_short' => env('CON_NAME_SHORT'),
    'payment_timeframe' => env('CON_PAYMENT_TIMEFRAME'),
    'admin_group' => env('CON_IDP_GROUP_ADMIN'),
    'frontdesk_group' => env('CON_IDP_GROUP_FRONTDESK'),
];
