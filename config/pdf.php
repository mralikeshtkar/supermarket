<?php

return [
    'mode' => 'utf-8',
    'format' => 'A4-L',
    'default_font_size' => '12',
    'default_font' => 'iran',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 10,
    'margin_header' => 0,
    'margin_footer' => 0,
    'author' => '',
    'subject' => '',
    'keywords' => '',
    'creator' => 'Laravel Pdf',
    'display_mode' => 'fullpage',
    'tempDir' => base_path('../temp/'),
    'pdf_a' => false,
    'pdf_a_auto' => false,
    'icc_profile_path' => '',
    'font_path' => base_path('resources/fonts/'),
    'font_data' => [
        'iran' => [
            'R' => 'IRANYekanRegular.ttf',
            'B' => 'IRANYekanBold.ttf',
            'useOTL' => 0xFF,
        ]
    ]
];
