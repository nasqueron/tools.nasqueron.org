<!DOCTYPE html>

<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8" />

  <!-- Set the viewport width to device width for mobile -->
  <meta name="viewport" content="width=device-width" />

  <title>Tools</title>

  <!-- Included CSS Files (Uncompressed) -->
  <!--
  <link rel="stylesheet" href="stylesheets/foundation.css">
  -->

  <!-- Included CSS Files (Compressed) -->
  <link rel="stylesheet" href="stylesheets/foundation.min.css">

  <script src="javascripts/modernizr.foundation.js"></script>
  <link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT:regular,italic' rel='stylesheet' type='text/css'>
  <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,400italic' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="stylesheets/general_foundicons.css">
  <link rel="stylesheet" href="stylesheets/general_enclosed_foundicons.css">
  <link rel="stylesheet" href="stylesheets/social_foundicons.css">
  <link rel="stylesheet" href="stylesheets/accessibility_foundicons.css">
  <!--[if lt IE 8]>
    <link href="/playground/stylesheets/general_foundicons_ie7.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/playground/stylesheets/general_enclosed_foundicons_ie7.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/playground/stylesheets/social_foundicons_ie7.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/playground/stylesheets/accessibility_foundicons_ie7.css" media="screen" rel="stylesheet" type="text/css" />
  <![endif]-->
  <link rel="stylesheet" href="/stylesheets/calson_ampersand.css">
  <link rel="stylesheet" href="stylesheets/app.css">
  <style type="text/css">
     /* Temporary CSS to test the import TCL Variants stuff */

     #content { font-family: "OFL Sorts Mill Goudy TT"; }
     #content p { white-space: pre; margin: 0 0 4em 2em; font-size: 1.125em; }
}
  </style>
</head>
<body>
  <!-- Header -->
  <header>
  <div class="row">
    <div class="twelve columns">
      <h2><a href="/"><i class="general foundicon-tools"></i></a>English text variants MOTD</h2>
    </div>
  </div>
  <div class="row">
   <div class="six columns">
      <p><i class="accessibility foundicon-question"></i><br />
      Calls `fortune` and prints it as is, in Jive and Valspeak.
      </p>
   </div>
   <div class="six columns">
      <p class="right"><a href="/"><i class="accessibility foundicon-braille"></i></a><br />
      Tools — <em>Small utilities, gadgets and scripts to perform daily tasks.</em></p>
    </div>
  </div>
  </header>

  <!-- Body -->
  <section id="content"><div class="row">
    <div class="twelve columns">
      <div id="action-icons"><a href="?" title="Refresh this page"><i class="general foundicon-refresh"></i></a></div>
<?php
	$content = `/home/dereckson/public_html/variants.tcl`;
	$pos1 = strpos($content, '<h2>');
	$pos2 = strpos($content, '</body>');
	echo substr(str_replace('h2', 'h3', $content), $pos1, $pos2 - $pos1);
?>
    </div>
  </div></section>

  <!-- Call to Action Panel -->
  <section id="feedback">
  <div class="row" style="background-color: white; padding: 0.75em 1em;">
    <div class="twelve columns">

      <div class="panel">
        <h4>Get in touch!</h4>

        <div class="row">
          <div class="nine columns">
            <p>We'd love to hear from you, you attractive person you.</p>
          </div>
          <div class="three columns">
            <a href="#" class="radius button right">Contact Us</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  </section>

  <!-- Footer -->
  <footer><div class="row">
  <div class="twelve columns"><hr />
      <div class="row">
        <div class="three columns">
          <p>This site is a repository of tools.</p>
          <p>Source code should be available, under BSD license.</p>
        </div>
        <div class="three columns">
        	<dl>
			<dt>Gerrit</dt>
			<dd>Activity feeds</dd>
			<dd>RSS generator</dd>
        	</dl>
        </div>
        <div class="three columns">
        	<dl>
			<dt>Network</dt>
			<dd><a href="/network/mx.php">MX</a></dd>
        	</dl>
        </div>
        <div class="three columns">
        	<dl>
			<dt>Gadgets</dt>
			<dd><a href="/gadgets/motd-variations.php">MOTD in Jive & Valspeak</a></dd>
        	</dl>
        </div>
      </div>
  </div>
  <div class="twelve columns"><hr />
      <div class="row">
           <div class="six columns">
               <p><strong>Options :</strong> <a href="javascript:SetUITonality('dark');">dark mode</a> | <a href="javascript:SetUITonality('light');">light mode</a></p>
           </div>
      </div>
      <div class="row">
        <div class="six columns">
            <p><strong>Code:</strong> <a href="http://www.dereckson.be/">Dereckson</a> | <strong>Powered by</strong> <a href="http://keruald.sf.net">Keruald/Pluton</a> and <a href="http://foundation.zurb.com/">Foundation</a>.</p>
        </div>

        <div class="six columns">
            <ul class="link-list right">
              <li><a href="http://www.dereckson.be/tools">DcK Area's tools</a></li>
              <li><a href="http://www.espace-win.org/Tools">Espace Win's tools</a></li>
              <li><a href="http://www.toolserver.org/~dereckson/">Toolserver</a></li>
            </ul>
        </div>

      </div>
  </div>
  </div></footer>

  <script src="javascripts/foundation.min.js"></script>
  <script src="javascripts/jquery.cookie.js"></script>
  <script src="javascripts/app.js"></script>
</body>
</html>
