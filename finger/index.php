<?php
if (array_key_exists('user', $_REQUEST)) {
    require_once('FingerClient.php');
    $client = FingerClient::FromAddress($_REQUEST['user']);
    if ($client == null) {
        echo '<div class="alert-box alert">Invalid Finger address format.</div>';
    } elseif (!$client->Run()) {
        echo '<div class="alert-box alert">', $client->lastError, '</div>';
        unset($client);
    }
}
?>
<h2>Who?</h2>
<form>
<div class="row collapse">
  <div class="ten mobile-three columns">
    <input type="text" name="user" id="user" value="<?= $_REQUEST['user'] ?? "" ?>" placeholder="username@server" />
  </div>
  <div class="two mobile-one columns">
    <input type="submit" class="button expand postfix" value="Finger" />
  </div>
</div>
</form>
<?php
if (isset($client) && $client->rawResult) {
    echo "<h2>Finger $_REQUEST[user]</h2>";
    echo '<pre id="finger">', $client->rawResult, '</pre>';
}
