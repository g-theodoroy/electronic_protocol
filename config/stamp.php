<?php

/**
 * 
 *  Εδώ ρυθμίζουμε την εμφάνιση της στάμπας Αριθμού Πρωτοκόλλου
 * 
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


    // διαφάνεια true ή false

    'transparent' => true,


    // ρύθμιση font 

    // ένα αρχείο .ttf που βρίσκεται στον φάκελο public/fonts ( γράφω μπροστά και το 'fonts/' )  // default Arialbd.ttf

    'font' => 'fonts/Arialbd.ttf',

    // μέγεθος γραμματοσειράς

    'font_size' => 8,



    //--------------------------------

    // ρύθμιση της θέσης της στάμπας


    // στο πάνω (header) ή κάτω (footer) μέρος της σελίδας
    // default header

    'section' => 'header',
    //'section' => 'footer',

    // στοίχιση left ή right    // default left

    'align' => 'left',
    //'align' => 'right',


    // απόσταση από σελίδα 

    // x αριστερά - δεξιά       // default 30
    // y πάνω - κάτω            // default 25

    'x' => 30,
    'y' => 25,


    // μόνο στα pdf 
    // διαφάνεια                // default 1.0

    'opacity' => 1.0,


    // μόνο στα docx
    // μόνο στην 1η σελίδα      // default false

    'onlyFirstPage' => false

];
