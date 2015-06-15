<?php

class FilesListingController {
	///
	/// Private members and properties
	///

	/**
	 * QueryResult
	 * @var array
	 */
	private $result;

	/**
	 * The directory to query
	 * @var string
	 */
	public $directory;

	/**
	 * The extension of files to query
	 * @var string
	 */
	public $extension;

	///
	/// Construtor
	///
	public function __construct ($directory, $extension) {
		$this->directory = $directory;
		$this->extension = $extension;
	}

	///
	/// Public methods
	///

	/**
     * Queries the files
	 *
	 * @return FilesListing the current class instance
	 */
	public function query () {
		$files = get_files($this->directory, $this->extension);

		//Prepends URL directory
		array_walk($files, function (&$file) {
			$file = get_url_for($this->directory, $file);
		});

		$this->result = $files;
		return $this;
	}

	/**
	 * Outputs the result
	 */
	public function show () {
		header("Content-Type: application/json");
		echo json_encode($this->result);
	}
}
