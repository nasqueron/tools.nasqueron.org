<?php

/**
 * Represents a controller to process the Thimbl client request
 */
class ThimblController {
    ///
    /// Constants
    ///

    /**
     * The path to the server to not finger
     *
     * @const
     */
    const BLACKLIST_FILE = 'finger/blacklist.txt';

    ///
    /// Private properties
    ///

    /**
     * Thimbl document from a plan file
     *
     * @var ThimblDocument|null
     */
    private $thimblDocument = null;

    /**
     * A finger client instance used to fetch the plan file containing the Thimbl feed
     *
     * @var FingerClient
     */
    private $client;

    /**
     * Errors occured during request processing
     *
     * @var array
     */
    private $errors = [];

    /**
     * Runs the controller logic
     */
    public function run () {
        if (array_key_exists('user', $_REQUEST)) {
            $this->processThimblClientRequest();
        }

        if (count($this->errors)) {
            $this->printErrors();
        }

        $this->printInputForm();

        if ($this->thimblDocument !== null) {
            $this->printThimblFeed();
        }
    }

    /**
     * Processes Thimbl client request
     */
    public function processThimblClientRequest() {
        require_once('FingerClient.php');

        $this->client = FingerClient::fromAddress($_REQUEST['user']);
        if ($this->client === null) {
            $this->errors[] = 'Invalid Finger address format.';
            return;
        }

        $this->handleBlackList();

        if (!$this->client->Run()) {
            $this->errors[] = $this->client->lastError;
            return;
        }

        $this->client->Parse();
        if (!$planField = $this->client->Get('Plan')) {
            $this->errors[] = 'Finger connection successful, but there is no plan file.';
            return;
        }

        require_once('ThimblDocument.php');
        if (!$this->thimblDocument = ThimblDocument::FromJSON($planField)) {
            $this->printDebugPlanMessage($planField);
        }
    }

    /**
     * Prints debug .plan message
     *
     * @param string $planField
     */
    private function printDebugPlanMessage ($planField) {
        echo '<div class="alert-box alert">Finger connection successful, but the plan file format is not a Thimbl one.<br />';
        echo 'JSON error returned by the parser: ', json_last_error_msg();
        echo '</div>';
        echo "<h2>Plan file for $_REQUEST[user]</h2>";
        echo '<pre>', clean_string($planField), '</pre>';
    }

    /**
     * Handles blacklist
     */
    private function handleBlackList () {
        if (file_exists(self::BLACKLIST_FILE)) {
            $blackListedServers = explode(
                "\n",
                trim(
                    file_get_contents(self::BLACKLIST_FILE)
                )
            );
            $this->client->AddToBlacklist($blackListedServers);
        }
    }

    /**
     * Prints errors
     */
    public function printErrors () {
        foreach ($this->errors as $error) {
            echo '<div class="alert-box alert">', $error, '</div>';
        }
    }

    /**
     * Prints input form
     */
    public function printInputForm () {
        include('thimbl_inputForm.html');
    }

    public function printThimblFeed () {
        $plan = $this->thimblDocument;

        echo "<h2>Thimbl feed for $_REQUEST[user]</h2>";
        require_once('thimbl_feed.php');
    }
}

(new ThimblController())->run();
