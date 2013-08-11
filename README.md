# unFrame

a micro unframe for PHP 5.2 environment under The BSD 2-Clause License.

Start
------
- chmod 777 cache
- mv kernel/config.php.sample kernel/config.php
- touch cache/product.env

	- enable production environment
	- autoload environment/production.php after loaded config.php

Remark
------

`$config['init']['auto_load_folders']` supported auto load nested directories files

		# controller/admin/person_controller.php
		# using **new Admin_Person_Controller()**
		<?php
		class Admin_Person_Controller {
			public function __construct() {
				echo __CLASS__;
			}
		}
		?>

>

		# controller/person_controller.php
		# using **new Person_Controller()**
		<?php
		class Person_Controller {
			public function index() {
				echo __CLASS__."::".__FUNCTION__;
			}
	
			public function create() {
				echo __CLASS__."::".__FUNCTION__;
			}
		}
		?>

Function
------

### Helper method

import all files from kernel/library/phpmailer directory

	import("kernel.library.phpmailer.*");

format print_r result

	p( $uploaded_info, $resized_info, $cropped_info );

### Alias method

	t -> Language::translate

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
	Cookie::set("name", "username");
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

### Form

Form::open

	Form::open("index.php", array("multipart" => true, "name" => 'test'));

	<form action="index.php" method="post" name="test" enctype="multipart/form-data">

Form::hidden

	Form::hidden("id", "1");

	<input type="hidden" name="id" value="1" />

Form::input

	Form::input("username", "Name");

	<input type="text" name="username" value="Name" />
	
Form::password
	
	Form::password("password", "1234");

	<input type="password" name="password" value="1234" />

Form::select && Form::select

	$options = Form::option(array('1' => 'boy', '2' => 'girl'), 1);
	Form::select(
		"gender", 
		$options, 
		array(
		'first_option' => '', 
		'multiple' => 'multiple', 
		'disabled' => 'disabled'
	));

	<select multiple="multiple" disabled="disabled">
	<option value=""></option>
	<option value="1" selected="selected">boy</option>
	<option value="2">girl</option>
	</select>

Form::checkbox

	Form::checkbox("Name", "username", false); echo " username";
	Form::checkbox("Name", "Noell", true); echo " Noell";

	<input type="checkbox" name="Name" value="username" />
	<input type="checkbox" name="Name" value="Noell" checked="checked" />

Form::textarea
	
	Form::textarea("content", "用戶名");

	<textarea name="content" rows="5">用戶名</textarea>

Form::submit

	Form::submit("Save");

	<input type="submit" name="commit" value="Save" />

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

### Language

	Language::translate("Name: %s, Age: %s", "username", 18);

Name: username, Age: 18

	Language::translate("Name: %{name}, Age: %{age}", array("name" => "username", "age" => 18));

Name: username, Age: 18.00

	Language::translate("Name: %{name}s, Age: %{age}0.2f", array("name" => "username", "age" => 18), false);

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

### Route

***Must .htaccess support***

Return params table

	p(Route::map("/user/:id/:name#.*#"));

Using closure function show the content *PHP 5.3*

	Route::map("/user/:id/:name", function($id, $name) {
		echo $id.' - '.$name;
	});

Use custom regex rule like preg_match("/username/", xx);

	Route::map("/user/:id/:name#username#", function($id, $name) {
		echo $id.' - '.$name;
	});

Use class function show the content

	class User {
		function info($id, $name) {
			echo $id.' - '.$name;
		}
	}

	Route::map("/user/:id/:name", array(new User(), "info"));

### Router

Add auto route in index.php for "/:controller", "/:controller/:action" and "/:controller/:action/:id"

	require_once dirname(__FILE__).'/kernel/init.php';
	Router::instance()->route();

Enable and Create auto load folder in kernel/config.php for load controller and model

	'auto_load_folders' => array(
		APP_ROOT.'/controller',
		APP_ROOT.'/model',
	)

Controller named "[NAME]_controller.php" in APP_ROOT.'/controller' folder

	class Person_Controller {
		public function index() {
			echo __CLASS__."::".__FUNCTION__;
		}

		public function create() {
			echo __CLASS__."::".__FUNCTION__;
			echo "<br />";

			// Three params key will auto added
			echo $this->params['controller']."/".$this->params['action']."/".$this->params['id'];
		}
	}

### Secure

Add slashes in array value

	Secure::add_slash(array(
		'a' => "'....'",
	));

Encrypt string by slat

	Secure::encrypt_text("This is a test");
	Secure::encrypt_text("This is a test", "slat");

CSRF validation

	if (Request::is_post() === true) {
		if (Secure::validate_csrf_token(Request::post("csrf_token")) === true) {
			echo "Y";
		}else{
			echo "N";
		}
		
		exit;
	}

	Form::open("index.php");
	Form::hidden("csrf_token", Secure::generate_csrf_token());
	Form::submit("Save");

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
