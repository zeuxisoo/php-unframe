Paginate
===

	Paginate::init(array(
		'row_count' => 100,
		'per_page' => 10,		// Optional
	));

	$offset = Paginate::offset();
	
	Paginate::build();

***custom view and hide the total page***

	Paginate::build("default.html", false);