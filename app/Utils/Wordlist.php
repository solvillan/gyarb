<?php
/**
 * Created by PhpStorm.
 * User: rickard
 * Date: 2016-10-27
 * Time: 12:40
 */

namespace App\Utils;


class Wordlist
{

    const WORDLIST = 'en_US';

    public static function getWord()
    {
        $path = resource_path().'/wordlists/'.Wordlist::WORDLIST.'.txt';
        $file = fopen($path, 'r');
        $list = [];
        if ($file) {
            while (($line = fgets($file)) !== false) {
                $list[] = trim($line);
            }
        }
        fclose($file);

        if (count($list) > 0) {
            return $list[random_int(0, count($list))];
        } else {
            return "No wordlist!";
        }
    }

}