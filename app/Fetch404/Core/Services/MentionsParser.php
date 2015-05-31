<?php namespace Fetch404\Core\Services;

use Fetch404\Core\Models\User;

class MentionsParser
{

    /**
     * Mentions parser
     * By: fetch404
     * Date: 5/28/2015
     * License: MIT
     */
    private static $usersRepository;

    /**
     * Create a new instance of MentionsParser.
     *
     */
    public function __construct()
    {

    }

    /**
     * Parse the given HTML to include @username tags.
     *
     * @param string $value
     * @return string
     */
    public static function parse($value = '')
    {
        if (preg_match_all("/\@([A-Za-z0-9\-_!\.\s]+)/", $value, $matches))
        {
            $matches = $matches[1];
            foreach($matches as $possible_username)
            {
                $user = null;
                while((strlen($possible_username) > 0) && !$user)
                {
                    $user = User::where('name', '=', $possible_username)->first();
                    if ($user)
                    {
                        $value = preg_replace("/".preg_quote("@{$possible_username}", "/")."/", "<a href=\"{$user->profileURL}\"><img src=\"{$user->getAvatarURL(20)}\" height=\"25\" width=\"20\" style=\"margin-bottom: 6px;\" />&nbsp;{$possible_username}</a>", $value);
                        break;
                    }

                    // chop last word off of it
                    $new_possible_username = preg_replace("/([^A-Za-z0-9]{1}|[A-Za-z0-9]+)$/", "", $possible_username);
                    if ($new_possible_username !== $possible_username)
                    {
                        $possible_username = $new_possible_username;
                    }
                    else
                    {
                        break;
                    }
                }
            }
        }

        return $value;
    }
}