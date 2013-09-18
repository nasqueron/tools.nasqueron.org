<?php

/**
 * Thimbl document
 */
class ThimblDocument {
    /**
     * The Thimbl raw deserialized .plan data
     * @var Object
     */
    private $data;

    /**
     * Initializes a new instance of the ThimblDocument object
     *
     * @param Object $data An object matching a deserialized json Thimbl plan file
     */
    public function __construct ($data) {
        $this->data = $data;
    }

    /**
     * Cleans a string, for JSON decode error
     *
     * @param string &$string The string to clean
     */
    private static function ApplyCleanKludge (&$string) {
        $string = clean_string($string);

        //Issue for nknouf@zeitkunst.org
        $wrongText = <<<'END'
âM-^Y» @zeminlu: UNIX' Russian Roulette: sudo [ $[ $RANDOM % 6 ] == 0 ] && rm -rf / || echo âM-^@M-^\You liveâM-^@M-^]
END;
        $rightText = "@zeminlu: UNIX' Russian Roulette: sudo [ \$[ \$RANDOM % 6 ] == 0 ] && rm -rf / || echo";
        //$rightText = "x";
        $string = str_replace(trim($wrongText), $rightText, $string);
    }

    /**
     * Gets a new instance of the ThimblDocument object from a JSON string
     */
    public static function FromJSON ($string) {
        self::ApplyCleanKludge($string);
        $data = json_decode($string);
        return is_object($data) ? new ThimblDocument($data) : null;
    }

    /**
     * Gets the finger address
     *
     * @return string The finger address
     */
    public function GetAddress () {
        return $this->data->address;
    }

    /**
     * Gets the name
     *
     * @return string The name
     */
    public function GetName () {
        return $this->data->name;
    }

    /**
     * Gets the properties
     *
     * @return Array An array with the properties
     */
    public function GetProperties () {
        return (array)$this->data->properties;
    }

    /**
     * Gets the specified property
     *
     * @param string $property The property
     * @return string The property value
     */
    public function GetProperty ($property) {
        $properties = $this->GetProperties();
        if (array_key_exists($property, $properties)) {
            return $properties[$property];
        }
        return null;
    }

    /**
     * Gets the messages
     *
     * @return Array An array with the messages
     */
    public function GetMessages () {
        $messages = array();
        foreach ($this->data->messages as $message) {
            $messages[] = (array)$message;
        }
        return array_reverse($messages);
    }

    /**
     * Gets the people the current user follows
     *
     * @param int $avatarSize The avatar width
     * @param string $avatarFallbackStyle The avatar fallback Gravatar style ('mm', 'blank', '404', 'identicon', 'wavatar', 'retro')
     * @return Array An array with the followed people
     */
    public function GetFollowing ($avatarSize = 80, $avatarFallbackStyle = 'mm') {
        $following = array();
        foreach ($this->data->following as $contact) {
            $following[] = [
                'nick' => $contact->nick,
                'address' => $contact->address,
                'avatar' => self::GetGravatar($contact->address, $avatarSize, $avatarFallbackStyle)
            ];
        }
        return $following;
    }

    /**
     * Gets the gravatar
     *
     * @param string $email The e-mail address
     * @param int $size The avatar width
     * @param string $fallbackStyle The avatar fallback Gravatar style ('mm', 'blank', '404', 'identicon', 'wavatar', 'retro')
     * @return string The Gravatar URL, or if not available a fallback representation
     */
    private static function GetGravatar ($email, $size = 200, $fallbackStyle = 'mm') {
        $hash = md5(strtolower(trim($email)));
        return "//www.gravatar.com/avatar/$hash?s=$size&d=$fallbackStyle";
    }

    /**
     * Gets the avatar
     *
     * @param int $size The avatar width
     * @param string $fallbackStyle The avatar fallback Gravatar style ('mm', 'blank', '404', 'identicon', 'wavatar', 'retro')
     * @return string The Gravatar URL, or if not available a fallback representation
     */
    public function GetAvatar ($size = 200, $fallbackStyle = 'mm') {
        return self::GetGravatar($this->data->properties->email, $size, $fallbackStyle);
    }

    /**
     * Gets the unixtime from a Thimbl time
     *
     * @param long $time The date, in a Thimbl time representation
     * @return int The date, in a unixtime representation
     */
    public static function ThimblTimeToUnixtime ($time) {
        return strtotime($time);
    }

    /**
     * Formats a message
     *
     * @param string $message The message to format
     * @return string The formatted message
     */
    public function FormatMessage ($message) {
        $message = preg_replace(
            "/(?<!a href=\")(?<!src=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i",
            "<a href=\"\\0\">\\0</a>",
            $message
        );
        return $message;
    }
}

/*
Here a minimal representation of a deserialized Thimbl .plan file:

stdClass Object
(
    [messages] => Array
        (
            [0] => stdClass Object
                (
                    [text] => A message
                    [time] => 20130917204527
                )

        )

    [replies] => stdClass Object
        (
        )

    [address] => user@domain.tld
    [following] => Array
        (
            [0] => stdClass Object
                (
                    [nick] => dk
                    [address] => dk@telekommunisten.org
                )

        )

    [name] => user
    [properties] => stdClass Object
        (
            [website] => http://www.domain.tld
            [email] => user@domain.tld
            [mobile] => Mobile withheld
        )

)
*/
