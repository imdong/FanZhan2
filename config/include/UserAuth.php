<?php
// 可登陆用户列表，按照规则一行一条，用户名必须小写 密码(区分大小写)为md5加密后的结果(不区分大小写)
UserAuth::$controlPrefix = 'UserAuth_';
UserAuth::$adminUserList = array(
	'imdong' => 'a4ddd4c8c5c1909b7a0e19e39eadf417',
	'admin' => 'e10adc3949ba59abbe56e057f20f883e',	// 密码 123456
);

UserAuth::Check();	// 只有调用此函数才能 对页面登录信息进行验证

class UserAuth {	
	static $controlPrefix = 'UserAuth_';	//  如与程序其他程序使用有冲突 则修改此区分
	static $adminUserList = array();	// 可登陆用户列表，按照规则一行一条，用户名必须小写 密码(区分大小写)为md5加密后的结果(不区分大小写)

	// 验证页面状态 执行此方法才会开启
	static public function Check(){
		if(!isset($_SESSION)) session_start();	// 开启 SESSION

		// 判断是否有控制命令
		if(empty($_GET[self::$controlPrefix.'Cmd'])){
			// 未设置命令 则判断是否登录，未登录则跳转到登录页面，否则不做处理
			if(empty($_SESSION[self::$controlPrefix.'LoginAdminName'])){
				// 未登录，跳转到登陆页面
				self::Location('?'.self::$controlPrefix.'Cmd=Login');
			}
		}else{
			// 获取提交的cmd 命令并转化为小写
			$UserAuth_Cmd = strtolower($_GET[self::$controlPrefix.'Cmd']);
			// 命令为退出登录
			if($UserAuth_Cmd == 'logout'){
				unset($_SESSION[self::$controlPrefix.'LoginAdminName']); // 清空 SESSION 账号信息
				self::Location($_SERVER['PHP_SELF']);	// 跳转到主页
			}else if($UserAuth_Cmd == 'login'){	// 指令为登录
				// 判断是否Post 提交数据
				if(!empty($_POST['username'])){
					// 验证POST的账号密码正确性
					$username = empty($_POST['username']) ? '' : strtolower($_POST['username']);	// 账号转换为小写
					$password = empty($_POST['password']) ? '' : md5($_POST['password']);	// 密码转换为md5

					if(!empty(self::$adminUserList[$username]) && $password == self::$adminUserList[$username]){
						$_SESSION[self::$controlPrefix.'LoginAdminName'] = $username;
						self::Location($_SERVER['PHP_SELF']);
					}else{
						$LoginMsg = 'UserName or PassWord Error!';
					}
				}
				// 输出登录HTML 表单
				header("Content-type: text/html; charset=utf-8");
				if(!empty($LoginMsg)) echo $LoginMsg . '<br />';
				die('<form action="" method="POST">UserName:<input type="text" name="username"/><br />PassWord:<input type="password" name="password"/><br /><input type="submit" value="Login"/></form>');
			}
		}

	}

	// 获取登录菜单
	static public function GetMenu(){
		$username = $_SESSION[self::$controlPrefix.'LoginAdminName'];
		$prex = self::$controlPrefix;
		$retStr = "<div id=\"{$prex}info\" style=\"position: fixed; right: 0px; top: 0px;\">";
		$retStr.= "{$username}&nbsp;|&nbsp;<a href=\"?{$prex}Cmd=Logout\" >Logout</a><hr/></div>";
		return $retStr;
	}
	// 内部方法 跳转到地址
	function Location($Url){
		header('location: ' . $Url);
		exit;
	}
}
