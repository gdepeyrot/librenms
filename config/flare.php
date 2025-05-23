<?php

/*
 | !!!! DO NOT EDIT THIS FILE !!!!
 |
 | You can change settings by setting them in the environment or .env
 | If there is something you need to change, but is not available as an environment setting,
 | request an environment variable to be created upstream or send a pull request.
 */

use Spatie\FlareClient\FlareMiddleware\CensorRequestBodyFields;
use Spatie\FlareClient\FlareMiddleware\CensorRequestHeaders;
use Spatie\FlareClient\FlareMiddleware\RemoveRequestIp;
use Spatie\LaravelIgnition\FlareMiddleware\AddDumps;
use Spatie\LaravelIgnition\FlareMiddleware\AddEnvironmentInformation;
use Spatie\LaravelIgnition\FlareMiddleware\AddExceptionInformation;
use Spatie\LaravelIgnition\FlareMiddleware\AddJobs;
use Spatie\LaravelIgnition\FlareMiddleware\AddNotifierName;
use Spatie\LaravelIgnition\FlareMiddleware\AddQueries;

return [
    /*
     |
     |--------------------------------------------------------------------------
     | Flare API key
     |--------------------------------------------------------------------------
     |
     | Specify Flare's API key below to enable error reporting to the service.
     |
     | More info: https://flareapp.io/docs/general/projects
     |
     */

    'key' => env('FLARE_KEY', 'quYFBTFNKHLBqFCoeo5yDVOQNbs6muV1'),

    /*
     |--------------------------------------------------------------------------
     | Middleware
     |--------------------------------------------------------------------------
     |
     | These middleware will modify the contents of the report sent to Flare.
     |
     */

    'flare_middleware' => [
        RemoveRequestIp::class,
        AddNotifierName::class,
        AddEnvironmentInformation::class,
        AddExceptionInformation::class,
        AddDumps::class,
        /*
           AddLogs::class => [
               'maximum_number_of_collected_logs' => 200,
           ],
           */
        AddQueries::class => [
            'maximum_number_of_collected_queries' => 50,
            'report_query_bindings' => true,
        ],
        AddJobs::class => [
            'max_chained_job_reporting_depth' => 5,
        ],

        // add git information, but cache it unlike the upstream provider
        App\Logging\Reporting\Middleware\AddGitInformation::class,

        // Add more LibreNMS related info
        App\Logging\Reporting\Middleware\CleanContext::class,
        App\Logging\Reporting\Middleware\SetGroups::class,
        App\Logging\Reporting\Middleware\SetInstanceId::class,

        CensorRequestBodyFields::class => [
            'censor_fields' => [
                'password',
                'password_confirmation',
                // LibreNMS
                'username',
                'sysContact',
                'community',
                'authname',
                'authpass',
                'cryptopass',
            ],
        ],
        CensorRequestHeaders::class => [
            'headers' => [
                'API-KEY',
            ],
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Reporting log statements
     |--------------------------------------------------------------------------
     |
     | If this setting is `false` log statements won't be sent as events to Flare,
     | no matter which error level you specified in the Flare log channel.
     |
     */

    'send_logs_as_events' => false,
];
