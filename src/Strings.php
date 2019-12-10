<?php

namespace CodexSoft\Code\Strings;

class Strings
{

    public static function getStartedWith( $start, $string ) {
        if ( !self::startsWith($start, $string) )
            $string = $start.$string;
        return $string;
    }

    public static function getEndedWith( $ending, $string ) {
        if ( !self::endsWith($ending, $string) ) {
            $string = $string.$ending;
        }
        return $string;
    }

    public static function verifyStartsWith( $start, &$string ) {
        $string = self::getStartedWith( $start, $string );
    }

    public static function verifyEndsWith( $ending, &$string ) {
        $string = self::getEndedWith( $ending, $string );
    }

    /**
     * @param $input
     * http://stackoverflow.com/questions/1993721/how-to-convert-camelcase-to-camel-case
     *
     * @return string
     */
    public static function sneak( $input ) {
        return self::fromCamelCase( $input );
    }

    public static function fromCamelCase( $input, $glue = '_' ) {
        preg_match_all( '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches );
        $ret = $matches[0];
        foreach ( $ret as &$match ) {
            $match = $match == strtoupper( $match ) ? strtolower( $match ) : lcfirst( $match );
        }
        return implode( $glue, $ret );
    }

    /**
     * @param int $number
     * @param int $length
     *
     * @param string $filler
     *
     * @return string
     */
    public static function leadingZero( $number, $length, $filler = '0' ) {
        return str_pad( $number, $length, $filler, STR_PAD_LEFT );
    }

    public static function contains( $part, $string ) {
        return (bool) substr_count( $string, $part );
    }

    /**
     * @param $substring
     * @param $string
     *
     * @deprecated
     * TODO: проверить работоспособность
     * @return mixed
     */
    public static function rtrim( $substring, $string ) {
        //return (bool) ( $string, $part );
        return substr_replace( $string, '', -strlen( $substring ), strlen( $substring ) );
    }

    /**
     * @param $substring
     * @param $string
     *
     * @deprecated
     * TODO: проверить работоспособность
     */
    public static function ltrim( $substring, $string ) {
        //return (bool) ( $string, $part );
        substr_replace( $string, '', 0, strlen( $substring ) );
    }

    /**
     * проверяет, заканчивается ли строка искомой подстрокой
     *
     * @param $ending
     * @param $string
     *
     * @return bool|int
     */
    public static function endsWith( $ending, $string ) {

        // TODO: проверить что работает нормально!
        // exception 'ErrorException' with message 'preg_match(): Unknown modifier ')'' in C:\CODE\web\localhost\_SharedCode\Ware\System\Functions\bootstrap.php:1667
        if ( !$ending || !$string ) return false;

        // TODO: EXCAPING
        // TODO: Delimiter must not be numeric or backslash!
        if ( $ending === '/' ) $ending = '\/';
        return preg_match( '/^(.*'.$ending.')$/', $string );
        //		return preg_match('/^(.*'.$ending.')$/',$string);

        //		throw new Exception('Empty!');
    }

    /**
     * смотрит, начинается ли строка $in с подстроки $findme
     *
     * @param $findme
     * @param $in
     *
     * @return bool
     */
    public static function startsWith($findme, $in ) {

        // TODO: strncmp() ?
        //		TODO: UTF8::strpos() ???
        $position = strpos( $in, $findme );
        return ( $position !== false ) && ( $position === 0 );

    }

    public static function backslash2colon( $string ) {
        return str_replace( '\\', ':', str_replace( '/', ':', $string ) );
    }

    public static function slashes2backslashes( $string )
    {
        return str_replace( '/', '\\', $string );
    }

    public static function backslashes2slashes($string )
    {
        return str_replace( '\\', '/', $string );
    }

    /**
     * alias
     * @param $string
     *
     * @return string
     */
    public static function bs2s($string): string
    {
        return self::backslashes2slashes($string);
    }

    // Will convert /path/to/test/.././..//..///..///../one/two/../three/filename
    // to ../../one/three/filename
    public static function normalizePath( $path ) {
        $parts = [];// Array to build a new path from the good parts
        $path = str_replace( '\\', '/', $path );// Replace backslashes with forwardslashes
        $path = preg_replace( '/\/+/', '/', $path );// Combine multiple slashes into a single slash
        $segments = explode( '/', $path );// Collect path segments
        foreach ( $segments as $segment ) {
            if ( $segment != '.' ) {
                $test = array_pop( $parts );
                if ( is_null( $test ) )
                    $parts[] = $segment;
                else if ( $segment == '..' ) {
                    if ( $test == '..' )
                        $parts[] = $test;

                    if ( $test == '..' || $test == '' )
                        $parts[] = $segment;
                } else {
                    $parts[] = $test;
                    $parts[] = $segment;
                }
            }
        }
        return implode( '/', $parts );
    }

    /**
     * [If you haven't yet] been able to find a simple conversion
     * back to string from a parsed url, here's an example:
     *
     * $url = 'http://usr:pss@example.com:81/mypath/myfile.html?a=b&b[]=2&b[]=3#myfragment';
     * if ($url === unparse_url(parse_url($url))) {
     * print "YES, they match!\n";
     * }
     *
     * @param $parsed_url
     *
     * @return string
     */
    public static function unparse_url( $parsed_url ) {
        $scheme = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'].'://' : '';
        $host = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';
        $port = isset( $parsed_url['port'] ) ? ':'.$parsed_url['port'] : '';
        $user = isset( $parsed_url['user'] ) ? $parsed_url['user'] : '';
        $pass = isset( $parsed_url['pass'] ) ? ':'.$parsed_url['pass'] : '';
        $pass = ( $user || $pass ) ? "$pass@" : '';
        $path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
        $query = isset( $parsed_url['query'] ) ? '?'.$parsed_url['query'] : '';
        $fragment = isset( $parsed_url['fragment'] ) ? '#'.$parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     *
     * @author : Dennis T Kaplan
     *
     * @version : 1.0
     * Date : June 17, 2007
     * Function : reverse strstr()
     * Purpose : Returns part of haystack string from start to the first occurrence of needle
     * $haystack = 'this/that/whatever';
     * $result = rstrstr($haystack, '/')
     * $result == this
     *
     * @access public
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return string
     **/
    public static function rstrstr( $haystack, $needle ) {
        return substr( $haystack, 0, strpos( $haystack, $needle ) );
    }

    public static function backslashes2dots( $string ) {
        return str_replace( "\\", '.', $string );
    }

    /**
     * Namespaced\Class\Name => Namespaced-Class-Name
     *
     * @param $string
     *
     * @return mixed
     */
    public static function backslashes2hyphen( $string ) {
        return str_replace( "\\", '-', $string );
    }

    public static function file_join_path()
    {
        return implode( '/', func_get_args() );
    }

    public static function fio( $surname, $name = '', $patronymic = '' ) {
        return $surname.' '
            .( mb_strlen( $name ) ? mb_substr( $name, 0, 1 ) : '' ).'.'
            .( mb_strlen( $patronymic ) ? mb_substr( $patronymic, 0, 1 ) : '' ).'.';
    }

    public static function num2str( $num )
    {
        $nul = 'ноль';
        $ten = [
            ['', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
            ['', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
        ];
        $a20 = ['десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать'];
        $tens = [2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто'];
        $hundred = ['', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот'];
        $unit = [ // Units
            //			array('копейка' ,'копейки' ,'копеек',	 1),
            ['', '', '', 1],
            //			array('рубль'   ,'рубля'   ,'рублей'    ,0),
            ['', '', '', 0],
            //			array(') рубль'   ,') рубля'   ,') рублей'    ,0),
            ['тысяча', 'тысячи', 'тысяч', 1],
            ['миллион', 'миллиона', 'миллионов', 0],
            ['миллиард', 'милиарда', 'миллиардов', 0],
        ];

        $num = str_replace( ',', '.', $num ); // это мое добавление уже System

        [$rub, $kop] = explode('.', sprintf("%015.2f", floatval($num)));
        $out = [];
        if ( intval( $rub ) > 0 ) {
            foreach ( str_split( $rub, 3 ) as $uk => $v ) { // by 3 symbols
                if ( !intval( $v ) ) continue;
                $uk = sizeof( $unit ) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list( $i1, $i2, $i3 ) = array_map( 'intval', str_split( $v, 1 ) );
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ( $i2 > 1 ) $out[] = $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ( $uk > 1 ) $out[] = Strings::num2str_morph( $v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2] );
            } //foreach
        } else $out[] = $nul;
        $out[] = Strings::num2str_morph( intval( $rub ), $unit[1][0], $unit[1][1], $unit[1][2] ); // rub

        $sum = trim( preg_replace( '/ {2,}/', ' ', join( ' ', $out ) ) );
        return $sum;
    }

    /**
     * Склоняем словоформу
     * @ author runcore
     *
     * @param $n
     * @param $f1
     * @param $f2
     * @param $f5
     *
     * @return mixed
     */
    public static function num2str_morph( $n, $f1, $f2, $f5 ) {
        $n = abs( intval( $n ) ) % 100;
        if ( $n > 10 && $n < 20 ) return $f5;
        $n = $n % 10;
        if ( $n > 1 && $n < 5 ) return $f2;
        if ( $n == 1 ) return $f1;
        return $f5;
    }

    public static function replacePlaceholders($string, array $values = null) {
        return empty($values) ? $string : strtr($string, $values);
    }

    /**
     * Возвращает сумму прописью
     *
     * @author runcore
     * @uses num2str_morph(...)
     *
     * @param $num
     * @param bool $wrap
     *
     * @return mixed|string
     */
    //	public function num2str($num,$wrap = false) {
    public static function cost2str( $num, $wrap = false ) {
        $nul = 'ноль';
        $ten = [
            ['', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
            ['', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
        ];
        $a20 = ['десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать'];
        $tens = [2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто'];
        $hundred = ['', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот'];
        $unit = [ // Units
            ['копейка', 'копейки', 'копеек', 1],
            ['рубль', 'рубля', 'рублей', 0],
            //			array(') рубль'   ,') рубля'   ,') рублей'    ,0),
            ['тысяча', 'тысячи', 'тысяч', 1],
            ['миллион', 'миллиона', 'миллионов', 0],
            ['миллиард', 'милиарда', 'миллиардов', 0],
        ];
        $rubs = floor( $num ); // целое количество рублей (3051.62 -> 3051)
        $num = str_replace( ',', '.', $num ); // это мое добавление уже System
        //
        list( $rub, $kop ) = explode( '.', sprintf( "%015.2f", floatval( $num ) ) );
        $out = [];
        if ( intval( $rub ) > 0 ) {
            foreach ( str_split( $rub, 3 ) as $uk => $v ) { // by 3 symbols
                if ( !intval( $v ) ) continue;
                $uk = sizeof( $unit ) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list( $i1, $i2, $i3 ) = array_map( 'intval', str_split( $v, 1 ) );
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ( $i2 > 1 ) $out[] = $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ( $uk > 1 ) $out[] = Strings::cost2str_morph( $v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2] );
            } //foreach
        } else $out[] = $nul;
        $out[] = Strings::cost2str_morph( intval( $rub ), $unit[1][0], $unit[1][1], $unit[1][2] ); // rub
        $out[] = $kop.' '.Strings::cost2str_morph( $kop, $unit[0][0], $unit[0][1], $unit[0][2] ); // kop

        $sum = trim( preg_replace( '/ {2,}/', ' ', join( ' ', $out ) ) );
        if ( $wrap ) // если надо вывести как 7500 (семь тысяч пятьсот) рублей 00 копеек
        {
            $sum = $rubs.' ('.trim( preg_replace( '/ {2,}/', ' ', join( ' ', $out ) ) );
            $sum = str_replace( ' руб', ') руб', $sum );
        }
        return $sum;
        //		return '( '.trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    }

    /**
     * Склоняем словоформу
     * @ author runcore
     *
     * @param $n
     * @param $f1
     * @param $f2
     * @param $f5
     *
     * @return mixed
     */
    public static function cost2str_morph( $n, $f1, $f2, $f5 ) {
        $n = abs( intval( $n ) ) % 100;
        if ( $n > 10 && $n < 20 ) return $f5;
        $n = $n % 10;
        if ( $n > 1 && $n < 5 ) return $f2;
        if ( $n == 1 ) return $f1;
        return $f5;
    }
    //	public function morph($n, $f1, $f2, $f5) {

    /*
    public function utf8_strpos($haystack, $needle, $offset = null)
    {
        if ($offset === null or $offset < 0) $offset = 0;
        if (function_exists('mb_strpos')) return mb_strpos($haystack, $needle, $offset, 'utf-8');
        if (function_exists('iconv_strpos')) return iconv_strpos($haystack, $needle, $offset, 'utf-8');
        if (! function_exists('utf8_strlen')) include_once 'utf8_strlen.php';
        $byte_pos = $offset;
        do if (($byte_pos = strpos($haystack, $needle, $byte_pos)) === false) return false;
        while (($char_pos = utf8_strlen(substr($haystack, 0, $byte_pos++))) < $offset);
        return $char_pos;
    }

    */

    public static function uc_first( $str ) {
        $str[0] = strtr( $str,
            "abcdefghijklmnopqrstuvwxyz".
            "\x9C\x9A\xE0\xE1\xE2\xE3".
            "\xE4\xE5\xE6\xE7\xE8\xE9".
            "\xEA\xEB\xEC\xED\xEE\xEF".
            "\xF0\xF1\xF2\xF3\xF4\xF5".
            "\xF6\xF8\xF9\xFA\xFB\xFC".
            "\xFD\xFE\xFF",
            "ABCDEFGHIJKLMNOPQRSTUVWXYZ".
            "\x8C\x8A\xC0\xC1\xC2\xC3\xC4".
            "\xC5\xC6\xC7\xC8\xC9\xCA\xCB".
            "\xCC\xCD\xCE\xCF\xD0\xD1\xD2".
            "\xD3\xD4\xD5\xD6\xD8\xD9\xDA".
            "\xDB\xDC\xDD\xDE\x9F" );
        return $str;
    }

    /**
     * Превращение первой буквы в Строчную
     *
     * @param $string
     *
     * @return mixed
     */
    public static function BigFirstLetter( $string ) {

        /*
        АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ
        абвгдеёжзийклмнопрстуфхцчшщъыьэюя
        ABCDEFGHIJKLMNOPQRSTUVWXYZ
        abcdefghijklmnopqrstuvwxyz

        $string[0] = strtr($string[0],
            'абвгдеёжзийклмнопрстуфхцчшщъыьэюяabcdefghijklmnopqrstuvwxyz',
            'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯABCDEFGHIJKLMNOPQRSTUVWXYZ');

        */

        $big = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $small = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюяabcdefghijklmnopqrstuvwxyz';

        // echo UTF8::ucfirst($string);

        //if (UTF8::is_utf8($s)) echo UTF8::strlen($s);
        //if (UTF8::is_utf8($string)) echo ('ЭТО UTF!');

        //echo $string[0].' = '.strpos($small,$string[0]);
        //			$string[0]=$big[\UTF8::strpos($small,$string[0])];
        if ( strpos( $small, $string[0] ) )
            $string[0] = $big[strpos( $small, $string[0] )];
        //			$string[0]=$big[\UTF8::strpos($small,$string[0])];
        return $string;

    }

    public static function strtolower_utf8( $string ) {
        $convert_to = [
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u",
            "v", "w", "x", "y", "z", "à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï",
            "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ø", "ù", "ú", "û", "ü", "ý", "а", "б", "в", "г", "д", "е", "ё", "ж",
            "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы",
            "ь", "э", "ю", "я",
        ];
        $convert_from = [
            "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U",
            "V", "W", "X", "Y", "Z", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï",
            "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж",
            "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ы",
            "Ь", "Э", "Ю", "Я",
        ];

        return str_replace( $convert_from, $convert_to, $string );
    }

    /**
     * @param string $input
     * @param string $separator
     *
     * @return string
     */
    public static function camelize( $input, $separator = '_' ) {
        return str_replace( $separator, '', ucwords( $input, $separator ) );
    }

    /**
     * @param string $string
     *
     * @return mixed
     * todo: 'e' modifier is deprecated, should be rewritten via preg_replace_callback
     */
    public static function toCamelCase( $string ) {
        // underscored to upper-camelcase
        // e.g. "this_method_name" -> "ThisMethodName"
        return preg_replace( '/(?:^|_)(.?)/e', "strtoupper('$1')", $string );
    }

    // приводит строку к виду, чтобы в итоге из полученного имени можео было сделать имя для класса
    /**
     * @param string $entity_name
     *
     * @return mixed|string
     */
    public static function canonize_concept_name( $entity_name ) {

        /**
         * TODO: наверное стоит хранить и в CamelCase и в snake_case имя концепта
         * причем из snake в camel преобразовать проще...
         * cached_by_name - в snake или в camel будет?
         */

        if ( !is_string( $entity_name ) ) return '';

        // удаляем пробелы из начала и конца строки
        $entity_name = trim( $entity_name );

        // строка не должна быть пустой
        if ( !$entity_name ) return '';

        $canonized_name = ucwords( $entity_name );
        //			$canonized_name = \UTF8::ucwords($entity_name);
        $canonized_name = str_replace( " ", "", $canonized_name );

        return $canonized_name;

    }

    // название месяца на русском
    public static function getRusMonth( $month ) {
        if ( $month > 12 || $month < 1 ) return false;
        $aMonth = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
        return $aMonth[$month - 1];
    }

    // выдаст дату в формате «29» июля 2014
    public static function getReadableDate( $timestamp ) {

        return '«'.date( 'd', $timestamp ).'» '
            .Strings::getRusMonth( date( 'n', $timestamp ) ).' '
            .date( 'Y', $timestamp );

    }

    /**
     * Checks a string for UTF-8 encoding.
     * Copied from \PHPUnit\Util\Xml::isUtf8
     *
     * @param string $string
     *
     * @return bool
     */
    public static function isUtf8($string): bool
    {
        $length = \strlen($string);

        for ($i = 0; $i < $length; $i++) {
            if (\ord($string[$i]) < 0x80) {
                $n = 0;
            } elseif ((\ord($string[$i]) & 0xE0) == 0xC0) {
                $n = 1;
            } elseif ((\ord($string[$i]) & 0xF0) == 0xE0) {
                $n = 2;
            } elseif ((\ord($string[$i]) & 0xF0) == 0xF0) {
                $n = 3;
            } else {
                return false;
            }

            for ($j = 0; $j < $n; $j++) {
                if ((++$i == $length) || ((\ord($string[$i]) & 0xC0) != 0x80)) {
                    return false;
                }
            }
        }

        return true;
    }

}

