<?php

$controller = new FilesListingController(
    get_directory(__DIR__),
	'json'
);

$controller
	->query()
	->show();
