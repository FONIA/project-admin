<?php
namespace app\index\validate;
use think\Validate;
class Index extends Validate
{
	protected $rule=[
		['csrf','require|token:csrf','令牌不能为空|令牌错误'],
		['acount','require|alphaDash|length:6,12','账号不能为空|账号规则：字母和数字下划线|账号6-12位长度'],
		['password','require|alphaDash|length:6,12','密码不能为空|密码规则：字母和数字下划线|密码6-12位长度'],
		['id','require|alphaDash|length:1,20','项目编号不能为空|项目编号规则：字母和数字下划线|1-20位长度'],
		['type','require|length:1,20','项目类型不能为空|1-20位长度'],
		['custom','require|length:1,20','客户不能为空|1-20位长度'],
		['title','require|length:1,20','项目名称不能为空|1-20位长度'],
		['worker','require|checkwid:worker','工程师不能为空'],
		['stime','require|timecheck:stime','项目时间不能为空'],
		['etime','require|timecheck:etime','项目交付时间不能为空'],
		['pid','require|chkpid:pid','项目编号不能为空'],
		['rate','between:1,100','完成进度错误'],
		['step','between:0,20','项目节点错误'],
		['day','checkday:day'],
		['rater','checkrate:rater'],
		['diff','checkdiff:diff'],
		['pjdata','checkpj:pjdata'],
		['newuser','checkuser:newuser'],
		['data','chkdata:data'],
		['uchg','userchg:uchg'],
		['uptask','taskchk:uptask']
	];
	
	protected $scene=[
		'login'=>['acount','password','csrf'],
		'newpid'=>['id','type','custom','title','worker','stime','etime','csrf'],
		'edit'=>['pid','stime','etime','rate','step'],
		'pid'=>['pid'],
		'user'=>['acount','password','csrf'],
		'upload'=>['pid','diff','day','rater'],
		'newpj'=>['pjdata'],
		'add'=>['newuser'],
		'uploadsql'=>['data'],
		'uploadsql2'=>['data'],
		'uchg'=>['uchg'],
		'uptask'=>['uptask','pid']
	];
	
	protected function taskchk($value)
	{		
		if(empty($value)){return 'empty';}
		if(strlen($value[0])<0||strlen($value[1])<0||strlen($value[2])<0){return 'Please fill all input';}
		if(strlen($value[0])>50||strlen($value[1])>50||strlen($value[2])>50||strlen($value[3])>50||strlen($value[4])>50||mb_strlen($value[5])>50||strlen($value[8])>50||strlen($value[9])>50||strlen($value[10])>50||strlen($value[11])>50||strlen($value[12])>50||strlen($value[13])>50||strlen($value[15])>50||strlen($value[16])>50){
			return 'Top 17 input must be fewer than 50';
		}
		if(mb_strlen($value[17])>100||mb_strlen($value[18])>100||mb_strlen($value[19])>100){return 'Last three input fewer than 100';}
		if(strtotime($value[6])>strtotime($value[7])){return 'Date not Match';}
		if(!preg_match("/^[0-9a-zA-Z_]+$/",$value[14])){return 'Engineer is Empty';}
		return true;
	}
	
	protected function userchg($value)
	{		
		if(!preg_match("/^[0-9a-zA-Z]{6,12}$/",$value['pwd'])){return '密码只能为数字、字母,6-12位';}
		if(!in_array($value['sex'],['男','女'])){return 'sex not match';}
		if(!strtotime($value['time'])){return 'date not match';}
		if(!preg_match("/^[0-9]{11}$/",$value['phone'])){return 'phone not match';}
		if(!filter_var($value['email'], FILTER_VALIDATE_EMAIL)){return 'email not match';}
		foreach($value as $v){
			if(!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_.@\-\/]+$/u", trim($v))){return '内容只能为数字、字母、下划线、汉字、@、-';}	
		}
		return true;
	}
	
	protected function checkuser($value)
	{
		$value=json_decode($value,true);
		if(!preg_match("/^[0-9a-zA-Z]{6,12}+$/",$value[0])){return 'Uid not match';}
		if(!in_array($value[2],['男','女'])){return 'sex not match';}
		if(strlen($value[1])<0||strlen($value[3])<0||strlen($value[4])<0||strlen($value[7])<0||strlen($value[8])<0||strlen($value[9])<0){return 'fill input complete';}
		if(!strtotime($value[5])){return 'time not match';}
		if(!preg_match("/^[0-9]{11}$/",$value[6])){return 'phone not match';}
		return true;
	}
	
	protected function checkpj($value)
	{
		$value=json_decode($value,true);
		if(!preg_match("/^[0-9a-zA-Z_]+$/",$value[0])){return 'Pid not Match';}
		if(strlen($value[1])<0||strlen($value[2])<0||strlen($value[3])<0||strlen($value[7])<0||strlen($value[8])<0){return 'Please fill all input';}
		if(strtotime($value[7])>strtotime($value[8])){return 'Date not Match';}
		if(!preg_match("/^[0-9a-zA-Z_]+$/",$value[15])){return 'Engineer is Empty';}
		return true;
	}
	
	protected function checkdiff($value)
	{
		$value=json_decode($value,true);
		foreach($value as $k){
			if($k==''||$k==0){continue;}
			if(!is_numeric($k)){return 'Date must be Number';}
		}
		return true;
	}
	
	protected function checkday($value)
	{
		$value=json_decode($value,true);
		foreach($value as $k){
			if($k==''||$k==0){continue;}
			if(!is_numeric($k)){return 'Time diff must be Integer';}
		}
		return true;
	}
	
	protected function checkrate($value)
	{
		$value=json_decode($value,true);
		foreach($value as $k){
			if($k==''||$k==0){continue;}
			if(!is_numeric($k)){return 'Rate must be Number';}
			if($k>100||$k<0){return 'Number between 0 and 100';}
		}
		return true;
	}
	
	protected function chkpid($value)
	{
		if(!preg_match("/^[0-9a-zA-Z_]{1,20}+$/",$value)){return 'Pid not Match';}
		return true;
	}
	
	protected function timecheck($value)
	{
		return strtotime($value)?true:'日期格式不合法';
	}
	
	protected function checkwid($value)
	{
		$file=ROOT_PATH.'database/user/user.json';
		$data=json_decode(file_get_contents($file),true);
		return array_key_exists($value,$data)?true:'工程师ID不存在';
	}
	
	protected function chkdata($value)
	{
		if($value['type']==1){
			unset($value['type']);
			foreach($value as $f=>$k){
				foreach($k as $i=>$v){
					if(!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_.@,，。-]+$/u", trim($v))){return '请检查文档中第'.$f.'行，内容只能为数字、字母、下划线、汉字、标点符号';}
					switch($i){
						case 0:
							if(!preg_match("/^[0-9a-zA-Z_]{6,12}+$/",trim($v))){return '文档中第'.$f.'行员工编号非法！';}
						break;
						case 5:
						    if(!is_numeric(trim($v))){return '文档中第'.$f.'行日期非法';}
						break;
						case 6:
							if(!preg_match("/^[0-9]{11}+$/",trim($v))){return '文档中第'.$f.'行手机号非法';}
						break;
						case 7:
							if(!preg_match("/^[0-9a-zA-Z_.@]+$/",trim($v))){return '文档中第'.$f.'行邮箱非法';}
						break;						
					}
				}
		    }
		}else{
			unset($value['type']);
			foreach($value as $f=>$k){
				foreach($k as $i=>$v){
					if(!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_,@.，。-]+$/u", trim($v))){return '请检查文档中第'.$f.'行，内容只能为数字、字母、下划线、汉字、标点符号';}
					switch($i){
						case 0:
							if(!preg_match("/^[0-9a-zA-Z_]+$/",trim($v))){return '文档中第'.$f.'行项目编号非法！';}
						break;
						case 5:
						    if(!is_numeric(trim($v))){return '文档中第'.$f.'行数量非法';}
						break;
						case 7:
						case 8:
							if(!is_numeric(trim($v))){return '文档中第'.$f.'行日期非法';}
						break;
						case 9:
							if(!is_numeric(trim($v))){return '文档中第'.$f.'行图纸页数非法';}
						break;
						case 15:
							if(!preg_match("/^[0-9a-zA-Z_]{6,12}+$/",trim($v))){return '文档中第'.$f.'行员工编号非法！';}
						break;
					
					}
				}
		    }
		}
		return true;
	}
}
?>