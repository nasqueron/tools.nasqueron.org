<!DOCTYPE html>

<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8" />

  <!-- Set the viewport width to device width for mobile -->
  <meta name="viewport" content="width=device-width" />

  <title><?= $document->title ?> - Tools</title>

  <!-- Included CSS Files (Uncompressed) -->
  <!--
  <link rel="stylesheet" href="/stylesheets/foundation.css">
  -->

  <!-- Included CSS Files (Compressed) -->
  <link rel="stylesheet" href="/stylesheets/foundation.min.css">

  <script src="javascripts/modernizr.foundation.js"></script>
  <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,400italic' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="/stylesheets/general_foundicons.css">
  <link rel="stylesheet" href="/stylesheets/general_enclosed_foundicons.css">
  <link rel="stylesheet" href="/stylesheets/social_foundicons.css">
  <link rel="stylesheet" href="/stylesheets/accessibility_foundicons.css">
  <!--[if lt IE 8]>
    <link href="/stylesheets/general_foundicons_ie7.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/stylesheets/general_enclosed_foundicons_ie7.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/stylesheets/social_foundicons_ie7.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/stylesheets/accessibility_foundicons_ie7.css" media="screen" rel="stylesheet" type="text/css" />
  <![endif]-->
  <link rel="stylesheet" href="/stylesheets/caslon_ampersand.css">
  <link rel="stylesheet" href="/stylesheets/app.css">
  <?= $document->head ?>
</head>
<body>
  <!-- Header -->
  <header>
  <div class="row">
    <div class="twelve columns">
      <h1><a href="/"><i class="general foundicon-tools"></i></a><?= $document->title ?></h1>
    </div>
  </div>
  <div class="row">
   <div class="six columns">
      <?php if ($document->description) echo '<p><i class="accessibility foundicon-question"></i><br />'; ?>
      <?= $document->description ?>
      </p>
   </div>
   <div class="six columns">
      <p class="right"><a href="/"><i class="accessibility foundicon-braille"></i></a><br />
      Tools — <em>Small utilities, gadgets and scripts to perform daily tasks.</em></p>
    </div>
  </div>
  </header>

  <!-- Body -->
  <section id="content"<?php if (isset($_COOKIE["UITonality"]) && $_COOKIE["UITonality"] != "light") { echo " class=\"$_COOKIE[UITonality]\""; } ?>><div class="row">
