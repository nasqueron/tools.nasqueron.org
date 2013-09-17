<?php

/**
 * Performs operation from given a regular expression
 */
class RegexpFactory {
    /**
     * The regular expression
     * @var string
     */
    public $expression;

    /**
     * The last error
     * @var string
     */
    public $lastError;

    /**
     * Initializes a new instance of the RegexpFactory object.
     *
     * @param string The regular expression
     */
    function __construct ($expression) {
        $this->expression = $expression;
    }

    /**
     * Replaces an expression using regexp, a similar way Apache mod_rewrite and Nginx wreplace do.
     *
     * @param string $haystack The expression to perform a replacement on
     * @param string $replaceExpression The format of the result string
     * @return string The replaced string
     */
    function replace ($haystack, $replaceExpression) {
        return preg_replace($this->expression, $replaceExpression, $haystack);
    }

    /**
     * Encloses the regular expression with delimiters.
     */
    function addDelimiters () {
        $delimiters = ['/', '@', '#', '~', '+', '%', '♦', 'µ', '±', '☞'];
        //TODO: check if it's okay to use UTF-8 characters as delimiters
        foreach ($delimiters as $delimiter) {
            if (strpos($this->expression, $delimiter) === false) {
                $this->expression = $delimiter . $this->expression . $delimiter;
                return;
            }
        }
        throw new Exception("Can't delimite regexp $this->expression");
    }

    /**
     * Determines if the specified expression is valid.
     *
     * @return bool true if the expression is valid; otherwise, false.
     */
    public function isValid () {
        $this->lastError = '';
        set_error_handler('self::handleErrors');
        $result = preg_match($this->expression, null);
        restore_error_handler();
        if ($this->lastError === '' && $result === false) {
            $this->lastError = self::getPCREError();
        }
        return $result !== false;
    }

    /**
     * Callback for error handler
     *
     * @param int $errno he level of the error raised
     * @param string $errstr the error message
     * @return bool if false, the normal error handler continues
     */
    private function handleErrors ($errno, $errstr) {
        if ($errno == 2 && substr($errstr, 0, 14) == 'preg_match(): ') {
            $this->lastError = substr($errstr, 14);
            if (substr($this->lastError, 0, 20) == 'Compilation failed: ') {
                $this->lastError = ucfirst(substr($this->lastError, 20));
            }
            return true;
        }
        message_die(
                    GENERAL_ERROR,
                    $errstr . "<p>Please report this bug. This error should be handled by the regexp factory.</p>",
                    'Regexp factory error'
        );
    }

    /**
     * Gets a string with an English error message matching the last PCRE error.
     *
     * @return The error message of the last PCRE library error.
     */
    public static function getPCREError() {
        $errors = array(
            PREG_NO_ERROR               => 'No error',
            PREG_INTERNAL_ERROR         => 'There was an internal PCRE error',
            PREG_BACKTRACK_LIMIT_ERROR  => 'Backtrack limit was exhausted',
            PREG_RECURSION_LIMIT_ERROR  => 'Recursion limit was exhausted',
            PREG_BAD_UTF8_ERROR         => 'The offset didn\'t correspond to the begin of a valid UTF-8 code point',
            PREG_BAD_UTF8_OFFSET_ERROR  => 'Malformed UTF-8 data',
        );

        return $errors[preg_last_error()];
    }
}
?>
