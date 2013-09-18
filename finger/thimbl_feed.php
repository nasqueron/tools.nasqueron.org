  </div>
  <div class="row" id="plan">

    <!-- Main Feed -->
    <div class="nine columns">
<?php
    $messages = $plan->GetMessages();
    foreach ($messages as $message) {
?>
      <!-- Message -->
      <div class="row plan-message">
        <div class="two columns mobile-one"><img class="class=plan-message-avatar" src="<?= $plan->GetAvatar(80) ?>]" /></div>
        <div class="ten columns">
          <p class="plan-message-text"><?= $plan->FormatMessage($message['text']) ?></p>
          <p class="plan-message-date">— <?= strftime("%Y-%m-%d %X", ThimblDocument::ThimblTimeToUnixtime($message['time'])) ?></p>
        </div>
      </div>

      <hr />
<?php } ?>
<?php

$contacts = $plan->GetFollowing(80, 'identicon');
if (!count($contacts)) {
    echo '<p>', $plan->GetName(), " doesn't follow anyone.</p>";
} else {
    echo '<h2>Following</h2>';
    echo '<ul class="block-grid four-up">';
    foreach ($contacts as $contact ) {
        $url = '?user=' . urlencode($contact['address']);
        if ($contact['nick']) {
            $who = $contact['nick'];
        } else {
            $data = explode('@', $contact['address']);
            $who = $data[0];
        }
        echo "<li><a href='$url'><img src='$contact[avatar]' class='avatar' /><br />$who</a></li>";
    }
    echo '</ul>';
}
?>
    </div>

    <!-- Nav Sidebar -->
    <div class="three columns">
      <div class="panel">
        <div style="text-align: center;"><img id="avatar" src="<?= $plan->GetAvatar(200, 'identicon') ?>" /></div>
        <h5 id="name"><?= $plan->GetName(); ?></h5>
        <p>
            <?= $plan->GetProperty('email') ?><br />
            <i class="general foundicon-phone"></i> <?= $plan->GetProperty('mobile') ?>
        </p>
        <dl class="vertical tabs">
          <dd><a href="<?= $plan->GetProperty('website') ?>">Web site</a></dd>
          <dd><a href="mailto:<?= $plan->GetProperty('email') ?>">Send a mail</a></dd>
        </dl>

      </div>
    </div>
