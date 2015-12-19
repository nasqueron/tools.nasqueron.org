<?php

/**
 * Performs operation from given a regular expression
 */
class RegexpFactory {
    /**
     * The regular expressions
     * @var string[]
     */
    public $expressions;

    /**
     * The replacement expressions
     * @var string[]
     */
    public $replaceExpressions;

    /**
     * The last error
     * @var string
     */
    public $lastError;

    /**
     * Regular expression to delimit regexps/replaces
     */
    const DELIMITER = '/\r\n|\n|\r/';

    /**
     * Initializes a new instance of the RegexpFactory object.
     *
     * @param string $rexpressions The regular expression
     * @param string $replaceExpressions The format of the result string
     */
     function __construct ($expressions, $replaceExpressions) {
        $this->expressions = preg_split(static::DELIMITER, $expressions);
        $this->replaceExpressions = preg_split(static::DELIMITER, $replaceExpressions);

        if (count($this->expressions) != count($this->replaceExpressions)) {
            throw new Exception("The number of expressions and replacements should match.");
        }
    }

    /**
     * Replaces an expression using regexp, a similar way Apache mod_rewrite and Nginx wreplace do.
     *
     * @param string $haystack The expression to perform a replacement on
     * @return string The replaced string
     */
    function replace ($haystack) {
        $text = $haystack;
        for ($i = 0 ; $i < count($this->expressions) ; $i++) {
            $expression = $this->expressions[$i];
            $replaceExpression = $this->replaceExpressions[$i];
            $text = preg_replace($expression, $replaceExpression, $text);
        }
        return $text;
    }

    /**
     * Adds delimiters to each regular expression.
     */
    public function addDelimiters () {
        array_walk($this->expressions, function (&$item) {
            $item = RegexpFactory::addDelimitersToExpression($item);
        });
    }

    /**
     * Encloses the specified regular expression with delimiters.
     *
     * @param string $expression The expression to delimit
     * @return string The expression with delimiters
     */
    public static function addDelimitersToExpression ($expression) {
        $delimiters = ['/', '@', '#', '~', '+', '%', '♦', 'µ', '±', '☞'];
        //TODO: check if it's okay to use UTF-8 characters as delimiters
        foreach ($delimiters as $delimiter) {
            if (strpos($expression, $delimiter) === false) {
                return $delimiter . $expression . $delimiter;
            }
        }
        throw new Exception("Can't delimite regexp $expression");
    }

    /**
     * Determines if the specified expression block is valid.
     *
     * @return bool true if each expression is valid; otherwise, false.
     */
    public function isValid () {
        foreach ($this->expressions as $expression) {
            if (!$this->isValidExpression($expression)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Determines if the specified expression is valid.
     *
     * @param string the regexp to test
     * @return bool true if the expression is valid; otherwise, false.
     */
    public function isValidExpression ($expression) {
        $this->lastError = '';
        set_error_handler('self::handleErrors');
        $result = preg_match($expression, null);
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
