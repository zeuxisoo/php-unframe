<?php
if (defined('IN_APP') === false) exit('Access Dead');

$lang = array(
	// clock.php
	'%{limit} second ago' => '%{limit} 秒前',
	'%{miute} minute %{second} second ago' => '%{miute} 分 %{second} 秒前',
	'%{hour} hour %{minute} minute ago' => '%{hour} 時 %{minute} 分前',
	'%{day} day ago' => '%{day} 天前',

	// upload.php
	'Can not remove file' => '刪除檔案失敗',
	'File format not allow' => '檔案格式不正確',
	'Can not write file' => '不能寫入檔案',
	'Overwrite disabled and file exists' => '檔案己存在 (自動覆寫目前為關閉)',
	'Upload single file success' => '上載單一檔案完成',
);
