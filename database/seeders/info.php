<?php

use Illuminate\Support\Facades\DB;

DB::table('infos')->insert([
    [
        'logo' => 'theater_favicon.png',
        'name' => 'Cinema',
        'phone' => '0123456789',
        'email' => 'hmcinema@gmail.com',
        'facebook' => 'https://www.facebook.com/',
        'twitter' => 'https://twitter.com/',
        'instagram' => 'https://www.instagram.com/',
        'youtube' => 'https://www.youtube.com/',
        'worktime' => 'Time: 06:00 - 23:00',
        'copyright' => '© Quang Truong, 2026. All rights reserved. Done by Quang Truong'

    ]
]);
