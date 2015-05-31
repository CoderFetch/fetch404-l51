<?php namespace Fetch404\Core\Services;

class SmiliesParser {

    /**
     * Smileys parser
     *
     * @author fetch404
     */

    /**
     * Create a new SmiliesParser instance.
     *
     * @type mixed
     */
    public function __construct()
    {

    }

    /**
     * Parse smileys from the given HTML.
     *
     * @param string $value
     * @return string
     */
    public static function parse($value = '')
    {
        $smilies = array(
            ':)' => '/assets/img/smilies/smile.png',
            ':(' => '/assets/img/smilies/sad.png',
            ':D' => '/assets/img/smilies/grin.png',
            ':P' => '/assets/img/smilies/tongue.png',
            ';)' => '/assets/img/smilies/wink.png',
            ':/' => '/assets/img/smilies/pouty.png',
            ':|' => '/assets/img/smilies/pouty.png'
        );

        foreach($smilies as $key => $img)
        {
            $value = preg_replace("/(^|>|\s)(" . preg_quote($key, "/") . ")($|<|\s)/", "$1<img src='" . $img . "' />$3", $value);
        }

        return $value;
    }
}