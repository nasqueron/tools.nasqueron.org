<?php

define('FEEDS_DIRECTORY', $document->get_directory());

class FeedEntry {
	public function __construct ($title, $avatar, $file) {
		$this->title = $title;
		$this->avatar = $avatar;
		$this->file = $file;

		$data = [];
		if (preg_match("@/([a-f0-9\-]+)\.xml$@", $this->file, $data)) {
			$this->guid = $data[1];
		} elseif (preg_match("@/([a-zA-Z0-9_\.]+)\.xml$@", $this->file, $data)) {
			$this->guid = str_replace('.', '-', $data[1]);
		} else {
			dieprint_r($this->file);
		}
	}

	public $title;
	public $avatar;
	public $file;
	public $guid;
}

class FeedIndex {
	public $idPrefix;
	public $feeds;

	public function print_feeds ($feeds = null) {
		if ($feeds === null) $feeds = $this->feeds;
		foreach ($feeds as $feed) {
			echo "<li id='{$this->idPrefix}-{$feed->guid}'>";
			if ($feed->avatar) echo "<a href='$feed->file'><img src='$feed->avatar?s=64&d=identicon' class='avatar' /></a><br />";
			echo "<a href='$feed->file'>$feed->title</a></li>";
		}
	}
}

class UsersFeedIndex extends FeedIndex {
	public function __construct() {
		$this->idPrefix = "user";
		$xml = simplexml_load_file(FEEDS_DIRECTORY . '/user/index.xml');
		$feeds = array();
		foreach ($xml->feed as $feed) {
			$feeds[] = new FeedEntry(
				(string)$feed->title,
				(string)$feed->avatar,
				get_url_for(FEEDS_DIRECTORY, 'user', (string)$feed->file)
			);
		}
		$this->feeds = $feeds;
	}
}

class ProjectsFeedIndex extends FeedIndex {
	function __construct() {
		$this->idPrefix = "project";
		$this->feedBySection[0] = [];
		foreach ($this->sections as $section) {
			$this->feedBySection[$section] = [];
		}
		$dir = FEEDS_DIRECTORY . '/project';
		foreach (scandir($dir) as $file) {
			if (get_extension($file) == "xml") {
				$data = explode('.', $file);
				array_pop($data);
				$section = $data[0];
				if (!in_array($section, $this->sections)) { $section = 0; }
				$this->feedBySection[$section][] = new FeedEntry(
					str_replace('_', '-', implode('/', $data)),
					null,
					get_url_for(FEEDS_DIRECTORY, 'project', $file)
				);
			}
		}
	}

	public $sections = [
		"mediawiki",
		"operations"
	];

	public $feedsBySection = [];

	public function print_section_feeds ($section, $restrictTo = null, $exclude = null) {
		if ($restrictTo == null) {
			$feeds = $this->feedBySection[$section];
		} else {
			$feeds = array();
			foreach ($this->feedBySection[$section] as $feed) {
				if (string_starts_with($feed->title, $restrictTo)) {
					$feed->title = substr($feed->title, strlen($restrictTo));
					$feeds[] = $feed;
				}
			}
		}
		if ($exclude) {
			$keptFeeds = [];
			foreach ($feeds as $feed) {
				if (!string_starts_with($feed->title, $exclude)) {
					$keptFeeds[] = $feed;
				}
			}
			$feeds = $keptFeeds;
		}
		$this->print_feeds($feeds);
	}
}

$projectsIndex = new ProjectsFeedIndex();
$usersIndex = new UsersFeedIndex();
?>
    <!-- End Thumbnails -->
    <div class="four columns">
        <h2>Projects</h2>

<ul class="accordion">
  <li class="active">
    <div class="title">
      <h5>MediaWiki</h5>
    </div>
    <div class="content">
      <ul>
	<?= $projectsIndex->print_section_feeds('mediawiki', null, 'mediawiki/extensions/'); ?>
      </ul>
      <br />
      <span class="round label">Extensions</span>
      <ul>
	<?= $projectsIndex->print_section_feeds('mediawiki', 'mediawiki/extensions/'); ?>
      </ul>
    </div>
  </li>
  <li>
    <div class="title">
      <h5>Operations</h5>
    </div>
    <div class="content">
	<ul><?= $projectsIndex->print_section_feeds('operations'); ?></ul>
    </div>
  </li>
  <li>
    <div class="title">
      <h5>Misc</h5>
    </div>
    <div class="content">
	<ul><?= $projectsIndex->print_section_feeds(0); ?></ul>
    </div>
  </li>
 </ul>
 <div class="panel callout radius">
    <p>This web interface is in alpha stage.</p>
    <p>Final version will provide nice feeds for humans and tools, and lot of links.</p>
    <p>Currently, only raw XML files are linked.</p>
 </div>
    </div>

    <!-- Thumbnails -->
    <div class="eight columns">
	<h2>Contributors</h2>
        <ul class="block-grid four-up">
	<?php $usersIndex->print_feeds(); ?>
        </ul>
    </div>
