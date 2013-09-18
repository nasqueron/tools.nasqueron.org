<?php
if (array_key_exists('user', $_REQUEST)) {
    require_once('FingerClient.php');
    $client = FingerClient::fromAddress($_REQUEST['user']);

    $blackListFile = 'finger/blacklist.txt';
    if (file_exists($blackListFile)) {
        $client->AddToBlacklist(explode("\n", file_get_contents($blackListFile)));
    }
    if ($client == null) {
        echo '<div class="alert-box alert">Invalid Finger address format.</div>';
    } elseif (!$client->Run()) {
        echo '<div class="alert-box alert">', $client->lastError, '</div>';
    } else {
        $client->Parse();
        if (!$planField = $client->Get('Plan')) {
            echo '<div class="alert-box alert">Finger connection successful, but there is no plan file.</div>';
        } else {
            require_once('ThimblDocument.php');
            if (!$plan = ThimblDocument::FromJSON($planField)) {
                echo '<div class="alert-box alert">Finger connection successful, but the plan file format is not a Thimbl one.<br />';
                echo 'JSON error returned by the parser: ', json_last_error_msg();
                echo '</div>';
                echo "<h2>Plan file for $_REQUEST[user]</h2>";
                echo '<pre>', clean_string($planField), '</pre>';
            }
        }
    }
}
?>
<h2>Who?</h2>
<form>
<div class="row collapse">
  <div class="ten mobile-three columns">
    <input type="text" name="user" id="user" value="<?= $_REQUEST['user'] ?>" placeholder="username@server" />
  </div>
  <div class="two mobile-one columns">
    <input type="submit" class="button expand postfix" value="Finger" />
  </div>
</div>
</form>
<?php
if ($plan) {
    echo "<h2>Thimbl feed for $_REQUEST[user]</h2>";
    require_once('thimbl_feed.php');
}
?>
