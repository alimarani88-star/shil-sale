<?php

function convertPersianToEnglish($number)
{
    $number = str_replace('۰', '0', $number);
    $number = str_replace('۱', '1', $number);
    $number = str_replace('۲', '2', $number);
    $number = str_replace('۳', '3', $number);
    $number = str_replace('۴', '4', $number);
    $number = str_replace('۵', '5', $number);
    $number = str_replace('۶', '6', $number);
    $number = str_replace('۷', '7', $number);
    $number = str_replace('۸', '8', $number);
    $number = str_replace('۹', '9', $number);

    return $number;
}


function convertArabicToEnglish($number)
{
    $number = str_replace('۰', '0', $number);
    $number = str_replace('۱', '1', $number);
    $number = str_replace('۲', '2', $number);
    $number = str_replace('۳', '3', $number);
    $number = str_replace('۴', '4', $number);
    $number = str_replace('۵', '5', $number);
    $number = str_replace('۶', '6', $number);
    $number = str_replace('۷', '7', $number);
    $number = str_replace('۸', '8', $number);
    $number = str_replace('۹', '9', $number);

    return $number;
}



function convertEnglishToPersian($number)
{
    $number = str_replace('0', '۰', $number);
    $number = str_replace('1', '۱', $number);
    $number = str_replace('2', '۲', $number);
    $number = str_replace('3', '۳', $number);
    $number = str_replace('4', '۴', $number);
    $number = str_replace('5', '۵', $number);
    $number = str_replace('6', '۶', $number);
    $number = str_replace('7', '۷', $number);
    $number = str_replace('8', '۸', $number);
    $number = str_replace('9', '۹', $number);

    return $number;
}
function to_slug($string): string
{
    $translateKeywords = [
        'inverter' => 'اینورتر',
        'suninverter' => 'سانورتر',
        'power' => 'برق',
        'panel' => 'تابلو',
        'protective' => 'تجهیزات_حفاظتی',
        'cable' => 'کابل',
        'lighting' => 'روشنایی',
        'subpanel' => 'تابلو_فرعی',
        'mcb' => 'MCB',
        'meter' => 'کنتور',
        'relay' => 'رله',
        'fuse' => 'فیوز',
        'portablecable' => 'کابل_همراه',
        'solidcable' => 'کابل_مفتولی',
        'ledlamp' => 'چراغ_LED',
        'fluorescentlamp' => 'چراغ_فلورسنت',
        'threephasepanel' => 'تابلو_۳_فاز',
        'singlephasepanel' => 'تابلو_تک_فاز',
        'mcb16a' => 'MCB_۱۶A',
        'mcb32a' => 'MCB_۳۲A',
        'singlephasemeter' => 'کنتور_تک_فاز',
        'threephasemeter' => 'کنتور_سه_فاز',
        'thermalrelay' => 'رله_حرارتی',
        'voltagerelay' => 'رله_ولتاژ',
        'fuse10a' => 'فیوز_۱۰A',
        'fuse20a' => 'فیوز_۲۰A',
        'industrialpanel' => 'تابلو_صنعتی',
        'residentialpanel' => 'تابلو_مسکونی',
        'meterpanel' => 'تابلو_کنتوری',
        'distributionpanel' => 'تابلو_توزیع',
        'battery' => 'باطری',
        'solarbattery' => 'باتری_خورشیدی',
        'generator' => 'مولد_الکتریکی',
        'switches' => 'کلید_قطع_وصل',
        'pushbuttons' => 'کلید_فشاری',
        'solarequipment' => 'تجهیزات_خورشیدی',
        'measuringequipment' => 'تجهیزات_اندازه_گیری',
        'protectiveequipment' => 'تجهیزات_حفاظتی',
        'tools' => 'ابزارآلات',
        'other' => 'سایر_موارد',
        'miniaturecircuitbreaker' => 'کلید_مینیاتوری',
        'automaticswitch' => 'کلید_اتوماتیک',
        'dlb6' => 'كليد_مينياتوري_DLB6_1P',
        'isolator' => 'کلید_ایزولاتور',
        'contactor' => 'کلید_کنتاکتور',
        'nzc10-1p' => 'كليد_مينياتوري_NZC10_1P',
        'nzc10-3p' => 'كليد_مينياتوري_NZC10_3P',
        'nzb6-1p' => 'كليد_مينياتوري_NZB6_1P',
        'nz' => 'کلید_مینیاتوری_NZ',
        'tl' => 'کلید_مینیاتوری_TL',
        'tr' => 'کلید_مینیاتوری_TR',
        'tf' => 'کلید_مینیاتوری_TF',
        'fixedautomatic' => 'کلید_اتوماتیک_فیکس',
        'airbreaker' => 'کلید_اتوماتیک_هوایی',
        'motorizedbreaker' => 'کلید_اتوماتیک_موتور_دار',
        'smartelectronic' => 'کلید_اتوماتیک_هوشمند',
        'electronic' => 'کلید_اتوماتیک_الکترونیکی',
        'simpleelectronic' => 'کلید_اتوماتیک_الکترونیکی_ساده',
        'n-electronic' => 'کلید_اتوماتیک_الکترونیکی_N',
        'adjustable' => 'کلید_اتوماتیک_قابل_تنظیم',
        'simpleadjustable' => 'کلید_اتوماتیک_قابل_تنظیم_ساده',
        'n-adjustable' => 'کلید_اتوماتیک_قابل_تنظیم_N',
        'nz1p' => 'كليد_ایزولاتور_NZ_1P',
        'nz3p' => 'كليد_ایزولاتور_NZ_3P',
        'cl3p' => 'كليد_ایزولاتور_CL_3P',
        'cl1p' => 'كليد_ایزولاتور_CL_1P',
        'contactorse' => 'کنتاکتور_SE',
        'contactorsp' => 'کنتاکتور_SP_mini',
        'contactoraccessory' => 'تجهیزات_جانبی_کنتاکتور',
        'dlc6-1p' => 'كليد_مينياتوري_DLC6_1P',
        'dlc6-2p' => 'كليد_مينياتوري_DLC6_2P',
        'dlc6-3p' => 'كليد_مينياتوري_DLC6_3P',
        'nzc6-1p' => 'كليد_مينياتوري_NZC6_1P',
        'nzc6-2p' => 'كليد_مينياتوري_NZC6_2P',
        'nzc6-3p' => 'كليد_مينياتوري_NZC6_3P',
        'nzc6-1pn' => 'كليد_مينياتوري_NZC6_1p+N',
        'nzc6-3pn' => 'كليد_مينياتوري_NZC6_3p+N',
        'nzc10-1pn' => 'كليد_مينياتوري_NZC10_1p+N',
        'nzc10-3pn' => 'كليد_مينياتوري_NZC10_3p+N',
        'nzd6-1p' => 'كليد_مينياتوري_NZD6_1P',
        'nzd6-3p' => 'كليد_مينياتوري_NZD6_3P',
        'nzb10-1pn' => 'كليد_مينياتوري_NZB10_1p+N',
        'nzb10-1p' => 'كليد_مينياتوري_NZB10_1P',
        'nzb10-3p' => 'كليد_مينياتوري_NZB10_3P',
        'nzd10-3p' => 'كليد_مينياتوري_NZD10_3P',
        'nzb10-3pn' => 'كليد_مينياتوري_NZB10_3p+N',
        'nzb6-3p' => 'كليد_مينياتوري_NZB6_3P',
        'nzb6-1pn' => 'كليد_مينياتوري_NZB6_1p+N',
        'nzd10-1p' => 'كليد_مينياتوري_NZD10_1P',
        'tr1p-c6ka' => 'کلید_مینیاتوری_TR_1P_C_6_KA',
        'tr2p-c6ka' => 'کلید_مینیاتوری_TR_2P_C_6_KA',
        'tr3p-c6ka' => 'کلید_مینیاتوری_TR_3P_C_6_KA',
        'tlc6-1p' => 'كليد_مينياتوري_TLC6_1P',
        'tlc6-3p' => 'كليد_مينياتوري_TLC6_3P',
        'tlb6-1p' => 'كليد_مينياتوري_TLB6_1P',
        'tlb6-3p' => 'كليد_مينياتوري_TLB6_3P',
        'tlc10-1p' => 'كليد_مينياتوري_TLC10_1P',
        'tldc' => 'کلید_مینیاتوری_TL_DC',
        'tlc10-3p' => 'كليد_مينياتوري_TLC10_3P',
        'tl2p-c6ka' => 'کلید_مینیاتوری_TL_2P_C_6_KA',
        'tl2p-b6ka' => 'کلید_مینیاتوری_TL_2P_B_6_KA',
        'tl4p-c6ka' => 'کلید_مینیاتوری_TL_4P_C_6_KA',
        'tr4p-c6ka' => 'کلید_مینیاتوری_TR_4P_C_6_KA',
        'tr4p-c10ka' => 'کلید_مینیاتوری_TR_4P_C_10_KA',
        'tr1p-c10ka' => 'کلید_مینیاتوری_TR_1P_C_10_KA',
        'tr2p-c10ka' => 'کلید_مینیاتوری_TR_2P_C_10_KA',
        'tr3p-c10ka' => 'کلید_مینیاتوری_TR_3P_C_10_KA',
        'tf1p-c4_5ka' => 'کلید_مینیاتوری_TF_1P_C_4_5_KA',
        'tf2p-c4_5ka' => 'کلید_مینیاتوری_TF_2P_C_4_5_KA',
        'tf3p-c4_5ka' => 'کلید_مینیاتوری_TF_3P_C_4_5_KA',
        'tf1p-c6ka' => 'کلید_مینیاتوری_TF_1P_C_6_KA',
        'tf2p-c6ka' => 'کلید_مینیاتوری_TF_2P_C_6_KA',
        'tf3p-c6ka' => 'کلید_مینیاتوری_TF_3P_C_6_KA',
        'tf1p-c10ka' => 'کلید_مینیاتوری_TF_1P_C_10_KA',
        'tf2p-c10ka' => 'کلید_مینیاتوری_TF_2P_C_10_KA',
        'tf3p-c10ka' => 'کلید_مینیاتوری_TF_3P_C_10_KA',
        'fixedsimple' => 'کلید_اتوماتیک_فیکس_ساده',
        'fixeddc' => 'کلید_اتوماتیک_فیکس_DC',
        'airsimple' => 'کلید_اتوماتیک_هوایی_ساده',
        'pushswitch' => 'کلید_های_شستی',
        'bakelitepush' => 'کلید_های_شستی_باکالیتی',
        'cranepush' => 'کلید_های_شستی_جرثقیلی',
        'metalpush' => 'کلید_های_شستی_فلزی',
        'pushaccessory' => 'تجهیزات_جانبی_شستی',
        'hybridinverter' => 'اینورتر_هیبریدی',
        'lithiumbattery' => 'باتری_لیتیومی',
        'leadbattery' => 'باتری_سربی',
        'measurepanel' => 'تابلو_اندازه_گیری',
        'digitalthermometer' => 'دماسنج_دیجیتال',
        'powermeter' => 'پاورمتر',
        'energymeter' => 'انرژی_متر',
        'currenttransformer' => 'ترانس_جریان',
        'clampmeter' => 'انبر',
        'multimeter' => 'مولتی_متر',
        'toolbox' => 'جعبه_ابزار',
        'stabilizer' => 'استابلایزر',
        'singlestabilizer' => 'استابلایزر_تکفاز',
        'threestabilizer' => 'استابلایزر_سه_فاز',
        'voltageprotector' => 'محافظ_ولتاژ',
        'railterminal' => 'ترمینال_ریلی',
        'residualdevice' => 'محافظ_جان',
        'terminal' => 'ترمینال',
        'rccbdp' => 'كليد_محافظ_جان_DR-2P',
        'rccb4p' => 'كليد_محافظ_جان_DR-4P',
        'combinedrccb1p' => 'محافظ_جان_تکفاز',
        'combinedrccb3p' => 'محافظ_جان_سه_فاز',
        'branchterminal' => 'ترمینال_انشعابی',
        'screwconnector' => 'کانکتور_پیچی',
        'terminalaccessory' => 'لوازم_جانبی_ترمینال',
        'terminalconnector' => 'ترمینال_کانکتور',
        'dinrail' => 'شینه_ریلی',
        'terminalclip' => 'بست_اتصال_ترمینال',
        'floater' => 'فلوتر',
        'photosensor' => 'فتوسل',
        'insulator' => 'مقره',
        'rotaryswitch' => 'کلید_گردان',
        'pedalswitch' => 'پدال',
        'portablesocket' => 'شاخه_سیار',
        'industrialsocket' => 'سوکت_صنعتی',
        'cablegland' => 'گلند',
        'lockswitch' => 'قفل_کلید',
        'fusebox' => 'جعبه_فیوز',
        'metalpushswitch' => 'کلید_فشاری_فلزی',
        'industrialequipment' => 'تجهیزات_صنعتی',
        'singlephaseinverter' => 'اینورتر_تکفاز',
        'threephaseinverter' => 'اینورتر_سه_فاز',
        'solarpanel' => 'پنل_خورشیدی',
        'automaticfi' => 'موتور_کلید_اتوماتیک_FI',
        'automaticadel' => 'موتور_کلید_اتوماتیک_AD/EL',
        'bearingfan' => 'فن_بلبرینگی',
        'bushedfan' => 'فن_بوشی',
        'surgearrester' => 'سرج_استر',
        'isolatorswitcher' => 'سکسیونر',
        'changeover2p' => 'کلید_چنج_اور_دو_پل',
        'changeover4p' => 'کلید_چنج_اور_چهار_پل',
    ];

    $string  = str_replace( array_values($translateKeywords) , array_keys($translateKeywords), $string);
  return  \Illuminate\Support\Str::slug($string);
}




