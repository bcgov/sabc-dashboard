<?php

return [
    'mode' => '',
    'format' => 'Letter',
    'default_font_size' => '12',
    'default_font' => 'sans-serif',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 10,
    'margin_header' => 0,
    'margin_footer' => 0,
    'orientation' => 'P',
    'title' => 'StudentAidBC PDF',
    'author' => '',
    'watermark' => '',
    'show_watermark' => false,
    'watermark_font' => 'sans-serif',
    'display_mode' => 'fullpage',
    'watermark_text_alpha' => 0.1,
    //	'custom_font_dir'       => '',
    //	'custom_font_data'		=> [],
    'auto_language_detection' => false,
    'temp_dir' => 'storage/app/mpdf',

    'custom_font_dir' => base_path('resources/fonts/'), // don't forget the trailing slash!
    'custom_font_data' => [
        'arial' => [
            'R' => 'ArialUnicodeMS.ttf',    // regular font
            //            'B'  => 'ArialUnicodeMS.ttf',       // optional: bold font
            //            'I'  => 'ArialUnicodeMS.ttf',     // optional: italic font
            //            'BI' => 'ArialUnicodeMS.ttf' // optional: bold-italic font
        ],
        // ...add as many as you want.
    ],

];
