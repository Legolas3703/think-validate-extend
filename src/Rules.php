<?php
namespace Legolas3703\ThinkValidateExtend;
use think\Validate;
use think\facade\Config;

class Rules
{
	/**
	 * 初始化
	 */
	public function __construct()
	{
		// 加载配置
		$this->loadConfig();
		// 加载验证器扩展规则
		$this->loadExtensionRules();
 	}


	/**
	 * 加载配置
	 */
	private function loadConfig()
	{
		// 加载Composer包配置文件
		Config::load(__DIR__ . '/../config/lang.php', 'lang');
	}


	/**
	 * 加载验证器扩展规则
	 */
	private function loadExtensionRules()
	{
		// 获取类中的所有公共方法名（除当前方法）
		$class = new \ReflectionClass($this);
		$method_list_original = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
		$method_list = [];
		foreach($method_list_original as $key=>$val)
		{
			$method_name = $val->getName();
			if($method_name !== __FUNCTION__)
			{
				$method_list[] = $method_name;
			}
		}
		unset($method_list_original);

		// 将公共方法作为扩展规则，注入验证器
		Validate::maker(
			function($validate) use ($method_list)
			{
				foreach($method_list as $key=>$val)
				{
					$callable = [$this, $val];
					$validate->extend($val, $callable);
				}
			}
		);
	}


	/**
	 *
	 */
	public function isJson() : Bool
	{
		return false;
	}


	/**
	 *
	 */
	public function isJson2() : Bool
	{
		return false;
	}


	/**
	 *
	 */
	public function isJson3() : Bool
	{
		// return true;
		return false;
	}


}

?>
