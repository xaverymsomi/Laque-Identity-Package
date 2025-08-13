<?php
return [
    // Exact 3-digit national prefixes after country code 255
    '3digit' => [
        '074' => 'vodacom',
        '075' => 'vodacom',
        // Some deployments also allocate 076 for Vodacom; include if needed.
         '076' => 'vodacom',
        '068' => 'airtel',
        '078' => 'airtel',
        '065' => 'tigo',
        '071' => 'tigo',
        '062' => 'halotel',
        '073' => 'ttcl',
        '077' => 'zantel',
    ],
    // Two-digit families (fallback) after 255
    '2digit' => [
        '74' => 'vodacom',
        '75' => 'vodacom',
         '76' => 'vodacom',
        '68' => 'airtel',
        '78' => 'airtel',
        '65' => 'tigo',
        '71' => 'tigo',
        '62' => 'halotel',
        '73' => 'ttcl',
        '77' => 'zantel',
    ],
];
