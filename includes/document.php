<?php

/*
 * Keruald, core libraries for Pluton and Xen engines.
 * (c) 2010, Sébastien Santoro aka Dereckson, some rights reserved
 * Released under BSD license
 *
 * @package Pluton
 * @subpackage Pluton
 * @copyright Copyright (c) 2010-2011, Sébastien Santoro aka Dereckson
 * @license Released under BSD license
 * @version 0.1
 */
class Document {
    /**
     * URL, before any treatment
     */
    public $url;

    /**
     * Topic, with - as topic seperator
     */
    public $topic;

    /**
     * Article, without extension
     */
    public $article;

    /**
     * Extension, without .
     */
    public $extension;

    /**
     * HTTP status code (200, 404, etC.)
     */
    public $status;

    /**
     * Document title
     */
    public $title;

    /**
     * Document description
     */
    public $description;

    /**
     * Content to write in <head> block
     */
    public $head;

    /**
     * Content to write at the end of the document.
     * This is after the site footer.
     * To write something before, use a local _footer.php file.
     */
    public $footer;

    /**
     * If true, doesn't print the header
     * @var boolean
     */
    public $noheader = false;

    /**
     * If true, doesn't print the footer
     * @var boolean
     */
    public $nofooter = false;

    /**
     * Initializes a new instance of Session object
     */
    public function __construct ($url) {
        $this->url = self::clean_url($url);
        $this->get_document();
    }

    /**
     * Finds the document
     */
    private function find_document () {
        //URL matches an existing file or directory
        if (file_exists($this->url)) {
            if (is_dir($this->url)) {
                //Ensures we've a trailing /
                $url = (substr($this->url, -1) == '/') ? $this->url : $this->url . '/';
                if (file_exists($url . 'index.html')) {
                    $this->url = $url . 'index.html';
                } elseif (file_exists($url . 'index.php')) {
                    $this->url = $url . 'index.php';
                } else {
                    return false; //empty directory
                }
            }
            return true;
        }

        //Application known extensions
        $extension = get_extension($this->url);
        if ($extension == "query") {
            $file = $this->url . ".php";
            if (file_exists($file)) {
                $this->url = $file;
                $this->noheader = true;
                $this->nofooter = true;
                return true;
            }
        }

        //Tries other extensions
        $extensions_to_try = array('html', 'php');
        $pathinfo = pathinfo($this->url);
        foreach ($extensions_to_try as $ext) {
            $file = "$pathinfo[dirname]/$pathinfo[filename].$ext";
            if (file_exists($file)) {
                $this->url = $file;
                return true;
            }
        }

        //Handles homepages
        if ($this->is_homepage($this->url)) {
            $this->url = "_index/index.html";
            return true;
        }

        return false;
    }

    /**
     * Gets the URL of the entry point script
     */
    static function get_entry_point_url () {
        $backtrace = debug_backtrace();
        $entry_point = array_pop($backtrace)['file'];
        return substr($entry_point, strlen(getcwd()));
    }

    /**
     * Gets the document matching the URL
     */
    private function get_document () {
        global $Config;
        //Finds the document
        if ($this->find_document()) {
            $this->status = 200;
        } else {
            $this->url = $Config['Pages']['Error404']; //TODO: choose and document error implementation
            $this->status = 404;
        }


        //Fills info from URL
        $pathinfo = pathinfo($this->url);
        if (!$this->is_homepage()) {
            $this->topic = str_replace('/', '-', $pathinfo['dirname']);
            $this->article = $pathinfo['filename']; //PHP 5.2.0+
        }
        $this->extension = strtolower($pathinfo['extension']);
        $this->title = "[$this->article]";

        //Fills info from _documents.xml
        $this->get_description();
    }

    /**
     * Cleans specified URL
     *
     * @param string $url the URL to clean
     * @return string clean URL
     */
    public static function clean_url ($url) {
        global $Config;

        if ($Config['AllowTopicArticleRequest'] && self::hasTopicArticleRequest()) {
            //This legacy mode allows site with 2001 Pluton version like
            //espace-win.net to make a smoother transition.
            //Cf. www.w3.org/Provider/Style/URI.html Cool URIs don't change

            //Topic (?Topic=...)
            if (array_key_exists('Topic', $_REQUEST)) {
                $url  = str_replace('-', '/', $_REQUEST['Topic']) . '/';
            }
            //Article (&Article=...)
            if (array_key_exists('Article', $_REQUEST)) {
                $url .= $_REQUEST['Article'];
            } else {
                $url .= 'index';
            }
            //Extension (&ext=...)
            if (array_key_exists('ext', $_REQUEST)) {
                $url .= '.';
                $url .= $_REQUEST['ext'];
            } else {
                $url .= '.html';
            }

            return $url;
        }


        //Homepage?
        if ($url == '' || $url == '/' || $url == $Config['BaseURL'] || $url == $Config['BaseURL'] . '/') {
            return $Config['Homepage'];
        }

        return substr($url, 1);
    }

    /**
     * Determines if the HTTP request contains Topic, Article or ext parameters
     *
     * @return bool true if the HTTP request contains Topic, Article or ext parameters ; otherwise, false
     */
    public static function hasTopicArticleRequest () {
        return array_key_exists('Topic', $_REQUEST) || array_key_exists('Article', $_REQUEST) || array_key_exists('ext', $_REQUEST);
    }

    /**
     * Determines if the current document is the homepage.
     *
     * @return bool true if the current document is the homepage ; otherwise, false.
     */
    public function is_homepage () {
        global $Config;
        //return $this->url == $Config['Homepage'];
        if ( $this->url == $Config['Homepage']) return true;
        if ($this->topic == "_index" && substr($this->article, 0, 5) == "index") return true;
        return false;
    }

    /**
     * Gets footer file (_footer.php) path in the current or parent directories
     *
     * @return string the path to the current footer if found ; otherwise, null. (or null if no footer is found)
     */
    public function get_footer () {
        $dirs = explode('-', $this->topic);
        for ($i = count($dirs) ; $i > 0 ; $i--) {
            $footer = join($dirs, '/') . '/_footer.php';
            if (file_exists($footer)) {
                return $footer;
            }
            array_pop($dirs);
        }
        return null;
    }

    /**
     * Prints the document body
     */
    public function render_body () {
        global $Config, $Session, $CurrentUser;
        $document = $this;

        //Header content
        if (!$this->noheader) {
            $header = $this->get_directory() . '/_header.php';
            if (file_exists($header)) {
                include($header);
            }
        }

        //Includes file
        switch ($this->extension) {
            case 'txt':
                echo "<pre>";
                include($this->url);
                echo "</pre>";
                break;

            case 'png':
            case 'jpg':
            case 'gif':
            case 'svg':
            case 'bmp':
                echo "<div align=center><img src=\"$this->url\" /></div>";
                break;

            default:
                include($this->url);
        }

        //Footer
        if (!$nofooter && $footer = $this->get_footer()) {
            include($footer);
        }
    }

    /**
     * Prints the document
     *
     * Use this method if you don't wish to have access to any other global
     * variables than $Config, $Session and $CurrentUser.
     *
     * A more flexible method is the body of this method in _includes/body.php
     * and to add in your skin <?php include('_includes/body.php'); ?>
     */
    function render () {
        //Global variables for the header and the footer
        global $Config, $Session, $CurrentUser;
        $document = $this;

        //Headers
        if ($this->status == 404) {
            header("Status: 404 Not Found");
        }

        //HTML output
        $theme = $Config['Theme'];
        if (!$this->noheader) include("themes/$theme/header.php");
        $this->render_body();
        if (!$this->nofooter) include("themes/$theme/footer.php");
    }

    /**
     * Gets the document description
     */
    function get_description () {
        if ($this->status == 404) {
            $this->title = "404 Not Found";
            $this->description = "The requested resource hasn't been found.";
            return;
        }

        if ($description = self::get_description_from_documentsXml($this->topic, $this->article)) {
            $variables = [ 'title', 'description', 'head', 'footer' ];
            foreach ($variables as $variable) {
                if (isset($description->$variable)) {
                    $this->$variable = (string)$description->$variable;
                }
            }

            $shortTags = [ 'noheader', 'nofooter' ];
            foreach ($shortTags as $shortTag) {
                if (isset($description->$shortTag)) {
             $this->$shortTag = true;
                }
            }
        }
    }

    public static function get_description_from_documentsXml ($topic, $article) {
        $topicDocuments = str_replace('-', '/', $topic) . '/_documents.xml';
        if (file_exists($topicDocuments)) {
            $xml = simplexml_load_file($topicDocuments, null, LIBXML_NOCDATA);
            foreach($xml->document as $document) {
        if ($document->article == $article) {
                    return $document;
                }
            }
            return null;
        }
    }

    public function get_directory () {
        return str_replace('-', '/', $this->topic);
    }
}
