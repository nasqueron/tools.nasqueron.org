      <!-- Actions -->
      <div id="action-icons"><a href="?" title="Refresh this page"><i class="general foundicon-refresh"></i></a></div>

      <!-- Content -->
<?php
	$content = `/home/dereckson/public_html/variants.tcl`;
	$pos1 = strpos($content, '<h2>');
	$pos2 = strpos($content, '</body>');
        $content = str_replace('<h2>', '<h3>', $content);
	echo substr($content, $pos1, $pos2 - $pos1);
?>
