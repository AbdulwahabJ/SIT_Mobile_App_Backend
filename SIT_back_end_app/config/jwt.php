<?php

return [
    'secret' => env('JWT_SECRET'),
    'ttl' => 60, // الزمن بالدقائق
    'refresh_ttl' => 20160, // زمن التجديد بالدقائق
    'algo' => 'HS256',
    'required_claims' => ['jti', 'sub', 'iat', 'exp'],
    'blacklist_grace_period' => 10,
    'unhashed_claims' => [],
    'jwt' => [
        'enabled' => true,
    ],
];
