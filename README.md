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

Plugin
===

**Add/Remove filter**

	function bold($content) {
		return "<strong>".$content."</strong>";	
	}

	function italic($content) {
		return "<span style='font-style: italic'>".$content."</span>";	
	}

	function underline($content) {
		return "<span style='text-decoration: underline'>".$content."</span>";		
	}

	Plugin::add_filter("the_content", "bold");
	Plugin::add_filter("the_content", "italic");
	Plugin::add_filter("the_content", "underline");

	Plugin::remove_filter("the_content", "bold");

	echo Plugin::apply_filter("the_content", "This is a test");

**Add/Remove action**

	function show_hello() {
		echo "Hello";
	}

	function show_world() {
		echo "World";
	}

	function show_symbol() {
		echo "!!";
	}

	Plugin::add_action("the_content", "show_hello");
	Plugin::add_action("the_content", "show_world");
	Plugin::add_action("the_content", "show_symbol");

	Plugin::remove_action("the_content", "show_symbol");

	Plugin::do_action("the_content");