<?php

return [
    'base_domain' => env('TENANCY_BASE_DOMAIN', 'maaninvite.com'),
    'admin_subdomain' => env('TENANCY_ADMIN_SUBDOMAIN', 'admin'),
    'organizer_admin_path' => env('TENANCY_ORGANIZER_ADMIN_PATH', 'admin'),
    'allow_unknown_hosts' => (bool) env('TENANCY_ALLOW_UNKNOWN_HOSTS', true),
];

