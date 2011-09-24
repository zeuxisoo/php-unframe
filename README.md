Function
------

### Helper method

import all files from kernel/library/phpmailer directory

	import("kernel.library.phpmailer.*");

format print_r result

	format_print_r( $uploaded_info, $resized_info, $cropped_info );

### Alias method

	t -> Locale::translate

Core
------

### Benchmark

	$start = Benchmark::start();

	// ...

	Benchmark::compared_time($start);

### Cache

Create
	
	Cache::add("test", "No");

Read
	
	Cache::get("test");

Update
	
	Cache::set("test", "Yes");

Delete

	Cache::delete("test");

### Clock

	Clock::to_date_time(1234567890, 'Y-m-d H:i:s (D)');
	Clock::human_time(Clock::to_timestamp("2011-08-21 19:36:00"))

### Cookie

	Cookie::get("name");
	Cookie::set("name", "zeuxis");
	Cookie::remove("name");

### Database

	$db = Database::instance();

	$db->result($db->query("SELECT VERSION"), 0);

	$query = $db->query("SELECT * FROM email_addons");
	while($row = $db->fetch_array($query)) {
		$row['checkpermission'];
	}

### Email

	Email::valid("test@gmail.com");

	Email::send(array(
		'to_mail' => 'test@gmail.com',
		'subject' => 'a',
		'message' => 'a',
	));

### Image

Upload single image first

	$uploaded_info = Upload::instance(array(
		'allow_format' => array('gif','jpg','jpeg','png'),
		'save_root' => ATTACHMENT_ROOT
	))->single_upload(Request::file("file"));

Not overwrite same name image, resized file save to save_root

	Image::instance(array(
		'save_root' =>  ATTACHMENT_ROOT.'/r',
	))->single_resize($uploaded_info['saved_file']['path'], 200, 200);

Overwrite same name image by resized image

	Image::instance()->single_resize($uploaded_info['saved_file']['path'], 200, 200);

Add prefix in resized file name

	Image::instance(array(
		'save_root' =>  ATTACHMENT_ROOT,
		'prefix_name' => 'thumb_',
	))->single_resize($uploaded_info['saved_file']['path'], 200, 200);

--------

Upload multi image first

	$uploaded_infos = Upload::instance(array(
		'allow_format' => array('gif','jpg','jpeg','png'),
		'save_root' => ATTACHMENT_ROOT
	))->multi_upload(Request::file("file"));

Create multi image paths

	$new_uploaded_infos = array();
	foreach($uploaded_infos as $infos) {
		$new_uploaded_infos[] = $infos['saved_file']['path'];
	}

Overwrite all same name image by resized image, all file resize to 200x200

	Image::instance()->multi_resize($new_uploaded_infos, 200, 200);

---------

Upload single file, resize image and crop image

	$uploaded_info = Upload::instance(array(
		'allow_format' => array('gif','jpg','jpeg','png'),
		'save_root' => ATTACHMENT_ROOT
	))->single_upload(Request::file("file"));

	$resized_info = Image::instance(array(
		'save_root' => ATTACHMENT_ROOT,
		'prefix_name' => 'thumb_',
	))->single_resize($uploaded_info['saved_file']['path'], 200, 200);

	$cropped_info = Image::instance(array(
		'save_root' => ATTACHMENT_ROOT,
		'prefix_name' => 'crop_',
	))->crop($uploaded_info['saved_file']['path'], 100, 100);

### Locale

	Locale::translate("Name: %s, Age: %s", "Zeuxis", 18);

Name: Zeuxis, Age: 18

	Locale::translate("Name: %{name}, Age: %{age}", array("name" => "Zeuxis", "age" => 18));

Name: Zeuxis, Age: 18.00

	Locale::translate("Name: %{name}s, Age: %{age}0.2f", array("name" => "Zeuxis", "age" => 18), false);

### Paginate

	Paginate::init(array(
		'row_count' => 100,
		'per_page' => 10,		// Optional
	));

	$offset = Paginate::offset();

	Paginate::build();

custom view and hide the total page

	Paginate::build("default.html", false);

### Plugin

Add/Remove filter

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

Add/Remove action

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

### Request

	Request::post("page");
	Request::get("page");
	Request::cookie("page");
	Request::file("page");

### Secure

	Secure::add_slash(array(
		'a' => "'....'",
	));

### Session

	Session::set("error", "Not found");
	Session::get("error");

Get it then clean it

	Session::get("error", true);

### Table

Query record by condition, limited 0~5 and order by addon_id

	Table::fetch_all("email_addons", array(
		"order" => "addon_id DESC",
		"where" => array(
			'addon_id' => 'checkpermissions'
		),
		'offset' => 0,
		'size' => 5,
	));

Query one record by condition

	Table::fetch_one("email_addons", array(
		"where" => array(
			'addon_id' => 'checkpermissions',
			'installed' => 1
		)
	));

find_by_column

	Table::find_by_column("email_addons", "addon_id", "checkpermissions");

find_by_[COLUMN]

	$table = new Table("email_addons");
	$table->find_by_addon_id("checkpermissions");

find_by_[COLUMN] --- *(PHP 5.3)*

	Table::find_by_addon_id("email_addons", 'checkpermissions');

Count record by different condition style

	Table::count("email_addons", array(
		'addon_id' => 'checkpermissions'
	));

	Table::count("email_addons", array(
		'where' => array(
			'addon_id' => 'checkpermissions'
		)
	));

	Table::count("email_addons", "WHERE addon_id = 'checkpermissions'");

	Table::count("email_addons", "addon_id = 'checkpermissions'");

	Table::count("email_addons", array(
		'where' => array(
			'addon_id' => 'checkpermissions',
			'installed' => 1
		),
		'where_logic' => 'OR'
	));

### Upload

Single upload

	Upload::instance(array(
		'allow_format' => array('gif','jpg','jpeg','png'),
		'save_root' => ATTACHMENT_ROOT
	))->single_upload(Request::file("file"));
	
Multi upload

	Upload::instance(array(
		'allow_format' => array('gif','jpg','jpeg','png'),
		'save_root' => ATTACHMENT_ROOT
	))->multi_upload(Request::file("file"));

### Url

	Url::php_self();
	
	Url::php_uri();
	
	Url::build('/', array(
		'action' => 'read',
		'id' => 1
	));

	Url::redirect('/');

### Util

	Util::string_length_by_utf8("aaa");
	
	Util::substring_by_utf8("aaa", 0, 1);

	Util::client_ip();

	Util::random_string(8);

	Util::size_format(Util::folder_size('/'));

### View

	include View::display("index.html");

Auto render by current script

	include View::display();