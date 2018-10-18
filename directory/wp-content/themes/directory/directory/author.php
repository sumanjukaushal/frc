<?php

/**
 * AIT WordPress Theme
 *
 * Copyright (c) 2012, Affinity Information Technology, s.r.o. (http://ait-themes.club)
 */


$latteParams['author'] = new WpLattePostAuthorEntity($wp_query->queried_object);

$latteParams['posts'] = WpLatte::createPostEntity(
	$wp_query->posts,
	array(
		'author' => $latteParams['author'] // if we have info about author inject it now
	)
);

WPLatte::createTemplate(basename(__FILE__, '.php'), $latteParams)->render();
