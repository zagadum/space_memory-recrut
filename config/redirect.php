<?php

if (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME']=='localhost' ||
    $_SERVER['SERVER_NAME']=='memory.localhost' ||
    $_SERVER['SERVER_NAME'] == 'memory.firm.kiev.ua' ||
    $_SERVER['SERVER_NAME'] == 'memory-pl.firm.kiev.ua' ||
    $_SERVER['SERVER_NAME'] == 'memory-en.firm.kiev.ua')) {


    return [
        'uk'=>'memory.firm.kiev.ua',
        'pl'=>'memory-pl.firm.kiev.ua',
        'en'=>'memory-en.firm.kiev.ua',
    ];


}else{
    return [
        'uk'=>'ua.space-memory.com',
        'pl'=>'pl.space-memory.com',
        'en'=>'en.space-memory.com',
    ];

}
