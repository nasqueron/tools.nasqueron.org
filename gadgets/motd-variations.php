     <!-- Extra fonts and layout -->
     <link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT:regular,italic' rel='stylesheet' type='text/css'>
     <style type="text/css">
       #content { font-family: "OFL Sorts Mill Goudy TT"; }
       #content p { white-space: pre; margin: 0 0 4em 2em; font-size: 1.125em; }
      </style>

      <!-- Actions -->
      <div id="action-icons"><a href="?" title="Refresh this page"><i class="general foundicon-refresh"></i></a></div>

      <!-- Content -->
<?php
	$content = `/home/dereckson/public_html/variants.tcl`;
	$pos1 = strpos($content, '<h2>');
	$pos2 = strpos($content, '</body>');
	echo substr($content, $pos1, $pos2 - $pos1);
?>
