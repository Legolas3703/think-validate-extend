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
	 * 验证是否为有效的JSON
	 * @access public
     * @param Srring $value 待检测的字符串
	 * @return Bool
	 */
	public function isJson(string $value) : Bool
	{
		$json = json_decode($value, true);
		return (is_array($json) && $json) ? true : false;
	}


	/**
	* 当A字段的值在列表中时，B字段为必填
	* @access public
	* @param Mixed $value B字段值
	* @param String $rule 验证规则 格式：字段A的名字,列表值1,列表值2...列表值n
	* @param Array $data 数据
	* @return Bool
	*/
	public function requireIn($value, string $rule, array $data=[]) : Bool
	{
		$rule_arr = explode(',', $rule);
		$field = array_shift($rule_arr);
		if( in_array($data[$field], $rule_arr) )
		{
			return empty($value) ? false : true;
		}
		return true;
	}

	/**
	* 验证值是否存在于指定表字段中
	* @access public
	* @param String $value 字段值
	* @param String $rule 验证规则 格式：数据表名（不含前缀）,字段名
	* @return Bool
	*/
	public function inTable(string $value, string $rule) : Bool
	{
		$rule_arr = explode(',', $rule);
		$table = $rule_arr[0];
		$field = $rule_arr[1];

		$db = app()->db->name($table);
		$check = $db->where($field, $value)->field('id')->find();

		return $check ? true : false;
	}


	/**
	* 验证值是否不存在于指定表字段中
	* @access public
	* @param String $value 字段值
	* @param String $rule 验证规则 格式：数据表,字段名
	* @return Bool
	*/
	public function notInTable(string $value, string $rule) : Bool
	{
		$check = $this->inTable($value, $rule);
		return $check ? false : true;
	}


	/**
	* 验证字符串字节数是否达到最小值
	* @access public
	* @param String $value 字段值
	* @param Int $rule 验证规则 格式：字节数
	* @return Bool
	*/
	public function minByte(string $value, int $rule) : Bool
	{
		$length = strlen($value);

		return $length >= $rule;
	}


	/**
	* 验证字符串字节数是否超过最大值
	* @access public
	* @param String $value 字段值
	* @param Int $rule 验证规则 格式：字节数
	* @return Bool
	*/
	public function maxByte(string $value, int $rule) : Bool
	{
		$length = strlen($value);

		return $length <= $rule;
	}


	/**
	* 验证字符串字节数，支持定长/区间验证
	* @access public
	* @param String $value 字段值
	* @param String $rule
	*	定长验证规则 格式：字节数（整数）
	*	区间验证规则 格式：最小值（整数）,最大值（整数）
	* @return Bool
	*/
	public function lengthByte(string $value, string $rule) : Bool
	{
		$length = strlen($value);

		// 区间验证
		if( strpos($rule, ',') )
		{
			// 长度区间
			list($min, $max) = explode(',', $rule);
			return ($length >= $min) && ($length <= $max);
		}

		// 指定长度
		return $length == $rule;
	}


	/**
	 * 过滤数组
	 * @access public
	 * @param Array $value 字段值
	 * @param String $rule 验证规则 [normal-普通模式，有非空值即可通过 strict-严格模式，禁止存在空值]
	 * @return Bool
	 */
	public function arrayFilter(array $value, string $rule) : Bool
	{
		switch($rule)
		{
			// 普通模式
			case 'normal':
				foreach($value as $key=>$val)
				{
					if( !empty($val) )
					{
						return true;
					}
				}
				return false;
			// 严格模式
			case 'strict':
				foreach($value as $key=>$val)
				{
					if( empty($val) )
					{
						unset($value[$key]);
						continue;
					}
				}
				return empty($value) ? false : true;
			// 未定义的模式，一律返回false
			default:
				return false;
		}
	}


}

?>
