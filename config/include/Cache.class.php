<?php
/**
* 文件缓存系统
*
* 普通Cache缓存类，适用于大量零碎小数据缓存。大文件缓存不适用。
*
* 特点：缓存文件分目录储存，防止单个目录下文件过多导致IO效率变低。
* 		可以控制子目录数，文件数始终在一个可以预见的数量范围之内。
*
* @author 青石 <www@qs5.org>
* @version 0.3 完成于 2015/10/7
*
*/
class Cache {

	// 定义类默认设置
	static $Config = array(
		'Cache_Dir'			=> './Cache',	// 默认保存目录
		'Cache_Dir_Chmod'	=> 0766,		// 新建缓存目录权限,建议 0666 可读写不可执行
		'File_Life'			=> 86400,		// 默认缓存有效时间 24小时 单位(s)
		'File_Suffix'		=> '.Cache.php',// 默认缓存文件名后缀 建议使用 .php 结尾
		'Group_Dir_Size'	=> -3,			// 文件夹分组命名字符节数	必须使用负数 数值越小 文件夹越多 单个文件夹内的缓存文件就越少
		'Group_File_Size'	=> 4,			// 文件名字命名字符节数	文件数会限制在 16的N次方 数字越大 缓存的文件数就越多
		'Group_Dir_Depr'	=> '_',			// 分类多级目录替换符 为空则不分级
		);

	// 保存缓存信息
	static public function save($Name, $Data, $Time = 0){
		if(strlen($Name) < 1) return false;	// 键名合法性检验

		$path = self::_getPathName($Name); // 生成键名 保存目录与文件名

		if(!self::_mkDir($path['dir'])){
			die('<br /><b>&#38169;&#35823;</b>: &#35831;&#26816;&#26597;&#32531;&#23384;&#25991;&#20214;&#22841;&#26159;&#21542;&#20855;&#26377;&#20889;&#26435;&#38480;&#12290;<br />');
		}

		// 获取缓存失效时间
		$Time = ($Time === NULL) ? -1 : (($Time < 0) ? abs($Time) : (($Time == 0) ? self::$Config['File_Life'] : $Time) + time());
		// 获取原来的缓存信息
		if(is_file($path['path'])){
			$file_str = file_get_contents($path['path']);
			$file_str = substr($file_str, 14);
			$file_info = unserialize($file_str);
		}
		// 生成需要保存的信息
		$file_info[$path['hash']] = array('n' => $Name, 't' => $Time, 'd' => $Data);
		$file_text = "<?php exit; ?>" . serialize($file_info);

		// 保存缓存信息到文件
		if(!file_put_contents($path['path'], $file_text, LOCK_EX)){
			return false;
		}
		return true;
	}

	// 读取缓存信息
	static public function read($Name){
		if(strlen($Name) < 1) return false;	// 键名合法性校验

		$path = self::_getPathName($Name);	// 生成键名 保存目录与文件名
		if(!is_file($path['path'])){
			return NULL;
		}
		// 获取缓存信息
		$file_info = file_get_contents($path['path']);
		$file_info = substr($file_info, 14);
		$ret_info = unserialize($file_info);
		// 取出对应的缓存
		if(!isset($ret_info[$path['hash']])){return NULL;}	// 不存在对应缓存
		$file_info = $ret_info[$path['hash']];
		// 判断缓存有效期
		if($file_info['t'] != -1 && $file_info['t'] < time()){
			self::del($Name);
			return NULL;
		}
		return $file_info['d'];
	}

	// 删除缓存信息
	static public function del($Name){
		if(strlen($Name) < 1) return false;	// 键名合法性校验

		$path = self::_getPathName($Name);	// 生成键名 保存目录与文件名
		if(!is_file($path['path'])){
			return true;
		}

		// 获取缓存信息
		$file_info = file_get_contents($path['path']);
		$file_info = substr($file_info, 14);
		$ret_info = unserialize($file_info);
		unset($ret_info[$path['hash']]);
		/* 删除失效的缓存文件 无意义 需要的时候在开启 有 BUG
		if(count($ret_info) <= 0){
			if(!unlink($path['path'])){
				die("<b>Error</b>: Create a cache directory fails, check whether write permission.");
			}
			if(rmdir($path['dir'])){
				die("Del dir OK");
			}else{
				die("Del dir NO");
			}
		}
		*/
		// 保存信息
		$file_text = "<?php exit; ?>" . serialize($ret_info);
		if(!file_put_contents($path['path'], $file_text, LOCK_EX)){
			return false;
		}
		return true;
	}

	// 私有方法 循环创建目录
	static private function _mkDir($dir, $mode = 0){
		if($mode == 0){$mode = self::$Config['Cache_Dir_Chmod'];}
		if(is_dir($dir)){	// 如果目录存在直接返回
			return true;
		}else if(!is_dir(dirname($dir)) && !self::_mkDir(dirname($dir), $mode)){	// 父目录不存在则先创建父目录
			return false;
		}
		return mkdir($dir, $mode);
	}

	// 私有方法 获取文件目录与关键字信息
	static private function _getPathName($Name){
		$path = explode('/', $Name);	// 分解名称
		if(count($path)<=1){
			$path = array('', $path[0]);	// 补全 分类标记
		}else{
			$path[0] = str_replace(self::$Config['Group_Dir_Depr'], '/', $path[0]) . '/';
		}

		// 获取 变量索引名 / 保存目录 /  缓存文件名
		$ret_path['hash'] = md5(strtolower($Name));
		$ret_path['dir'] = self::$Config['Cache_Dir'] . '/' . $path[0] . substr($ret_path['hash'], self::$Config['Group_Dir_Size']) . '/';
		$ret_path['name'] = substr($ret_path['hash'], 0, self::$Config['Group_File_Size']) . self::$Config['File_Suffix'];
		$ret_path['path'] = $ret_path['dir'] . $ret_path['name'];
		return $ret_path;
	}
}
// 封装成快捷调用方法 S();
function S($Name, $Data = '', $time = 0){
	if(strlen($Name) < 1) return false;	// 键名合法性校验
	if($Data === ''){
		return Cache::read($Name);	// 不设置 Data 为读取
	}elseif($Data == NULL){
		return Cache::del($Name);	// 设置为 NULL 为删除
	}else{
		return Cache::save($Name, $Data, $time);	// 否则为写入
	}
}