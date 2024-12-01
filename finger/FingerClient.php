<?php

/**
 * Finger client
 */
class FingerClient {
    /**
     * The finger remote server
     * @var string
     */
    public $server;

    /**
     * The finger remote user
     * @var string
     */
    public $user;

    /**
     * The finger raw result
     * @var string
     */
    public $rawResult;

    /**
     * The finger structured result
     * @var array
     */
    public $structuredResult;

    /**
     * Timeout time to connect to finger server
     * @var int
     */
    public $timeout = 10;

    /**
     * The last error
     * @var string
     */
    public $lastError;

    /**
     * Blocked hosts, where you can't finger
     * @var array
     */
    private $blockedHosts = [];

    /**
     * Initializes a new instance of the FingerClient object
     *
     * @param string server The finger remote server
     * @param string user The finger remote user
     */
    public function __construct ($server, $user) {
        $this->server = $server;
        $this->user = $user;
    }

    /**
     * Initializes a new instance of the FingerClient object from an address
     *
     * @param string $address The finger address
     * @return FingerClient a FingerClient instance; if the address is invalid, returns null.
     */
    public static function FromAddress ($address) {
        if (self::IsValid($address)) {
            $data = explode('@', $address, 2);
            return new self($data[1], $data[0]);
        }
        return null;
    }

    /**
     * Runs the finger client
     * After this method have been called, the rawResult member will be available.
     *
     * @return bool If the session gets finger information, true; otherwise, false.
     */
    public function Run () {
        if (in_array($this->server, $this->blockedHosts)) {
            $this->lastError = "This server $this->server has been blocked (probably because of frequent timeouts).";
            return false;
        }

        $fp = @fsockopen($this->server, 79, $errno, $errstr, $this->timeout);
        if ($fp === false) {
            $this->lastError = "Can't connect to $this->server. $errstr";
            return false;
        }
        fwrite($fp, $this->user);
        fwrite($fp, "\n");
        $this->rawResult = '';
        while(!feof($fp)) {
            $this->rawResult .= fgets($fp, 4096);
        }
        fclose($fp);

        if (trim($this->rawResult) == "finger: $this->user: no such user.") {
            $this->lastError = "No such user on this server.";
            return false;
        }

        return true;
    }

    /**
     * Parses the finger result to get structured data
     *
     * After this method have been called, the structuredResult member will be available.
     */
    public function Parse () {
        $lines = explode("\n", $this->rawResult);
        $fields = array();
        $n = count($lines);
        $mode = ''; //Parsing mode ('' for Plan or simpler fields), 'project' for the project)

        for ($i = 0 ; $i < $n ; $i++) {
            $line = $lines[$i];
            if ($mode == 'project') {
                if ($i == $n - 1 || trim($line) == 'Plan:' || trim($line) == 'No Plan.') {
                    //Ends project mode
                    $mode = '';
                    $fields[] = ['field' => 'Project', 'data' => $project];
                } else {
                    $project .= $line;
                    continue;
                }
            }

            if (strpos($line, "\t") !== false) {
                //This is probably a line with two fields (A: ...    B: ...).
                $data = explode("\t", $line, 2);
                for ($j = 0 ; $j < 2 ; $j++) {
                    $fields[] = self::ParseBlock($data[$j]);
                }
                continue;
            }

            if (trim($line) == 'Project:') {
                $mode = 'project';
                $project = '';
                continue;
            }

            if (trim($line) == 'No Mail.') {
                $fields[] = ['field' => 'Mail', 'data' => ''];
                continue;
            }

            if (trim($line) == 'No Plan.') {
                $fields[] = ['field' => 'Plan', 'data' => ''];
                continue;
            }

            if (trim($line) == 'No Project.') {
                $fields[] = ['field' => 'Project', 'data' => ''];
                continue;
            }

            if (trim($line) == 'Plan:') {
                $fields[] = [
                    'field' => 'Plan',
                    'data'  => implode("\n", array_slice($lines, ++$i))
                ];
                break;
            }

            if (trim($line) !== "") {
                $data = self::ParseBlock($line);
                $fields[] = $data;
            }
        }

        $this->structuredResult = $fields;
    }

    /**
     * Parses a string to get field and data information
     *
     * @param string $The finger line
     */
    private static function ParseBlock ($data) {
        $info = explode(': ', trim($data), 2);
        if (count($info) == 1) {
            return ['field' => '', 'data' => $info[0]];
        }
        return ['field' => $info[0], 'data' => $info[1]];
    }

    /**
     * Gets the specified field parsing the Finger raw result
     *
     * @param string $field The field to retrieve
     * @return string The field data.
     */
    public function Get ($field) {
        if ($this->structuredResult === null) return null;
        foreach ($this->structuredResult as $info) {
            if ($info['field'] == $field) {
                return $info['data'];
            }
        }
        return null;
    }

    /**
     * Determines if an address is syntactically valid
     *
     * @param string $address The finger address
     * @return bool true if the address is valid; otherwise, false.
     */
    public static function IsValid ($address) {
        return preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $address);
    }

    /**
     * Adds the specified hosts into the blocklist
     *
     * @param array the list of hosts to add to the blacklist
     */
    public function AddToBlocklist ($blockedHosts) {
        $this->blockedHosts = array_merge($this->blockedHosts, $blockedHosts);
    }

    /**
     * Clears the blocklist
     */
    public function ClearBlocklist () {
        $this->blockedHosts = [];
    }
}
