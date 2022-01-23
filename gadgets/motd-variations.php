      <!-- Actions -->
      <div id="action-icons">
          <a href="" title="Refresh this page">
              <i class="general foundicon-refresh"></i>
          </a>
      </div>

      <!-- Content -->
<?php
    $fortune = rtrim(`/usr/bin/fortune`);
    echo "      <h3>English text</h3>\n";
    echo "      <p>$fortune</p>\n\n";

    $variants = [
        'jive',
        'valspeak',
    ];

    foreach ($variants as $variant) {
        echo "      <h3>English variant — $variant</h3>\n";
        $text = escapeshellarg($fortune);
        echo "      <p>", rtrim(`echo $text | $variant`), "</p>\n\n";
    }
