<?php

/*
Εδώ ρυθμίζουμε την εμφάνιση της στάμπας Αριθμού Πρωτοκόλλου


*/

return [

    // ρύθμιση χρωμάτων

    // $textColor RGB Red-Green-Blue 0-255
    'txtR' => 0,
    'txtG' => 0,
    'txtB' => 255,
    // $borderColor
    'brdR' => 0,
    'brdG' => 0,
    'brdB' => 255,
    // $bgColor background
    'bgR' => 255,
    'bgG' => 255,
    'bgB' => 255,
    // transparent true ή false
    'transparent' => true,

    // Font size is in pixels.
    'font_size' => 8,

    // ρύθμιση της θέσης της στάμπας

    // κοινά και για τα δύο 
    'wrapDistanceTop' => 5,     // απόσταση από κορυφή σελίδας
    'wrapDistanceRight' => 15,  // απόσταση από δεξιά σελίδας
    'position' => [
        // για pdf  αριστερά - κέντρο - δεξιά
        //'pdf' => \Ajaxray\PHPWatermark\Watermark::POSITION_TOP_LEFT,
        //'pdf' => \Ajaxray\PHPWatermark\Watermark::POSITION_TOP,
        'pdf' => \Ajaxray\PHPWatermark\Watermark::POSITION_TOP_RIGHT,

        // για docx   αριστερά - κέντρο - δεξιά
        //'docx'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_LEFT,
        //'docx'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_CENTER,
        'docx'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_RIGHT,
    ]
];
