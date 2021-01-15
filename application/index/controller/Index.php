<?php
namespace app\index\controller;
use fonia\ac as Action;
use think\Debug;
class Index extends \think\Controller
{
    public function index()
    {           	
    	$this->assign(['sign'=>\think\config::get('info.sign'),'Author'=>\think\config::get('info.Author'),'version'=>\think\config::get('info.version'),'company'=>\think\config::get('info.company')]);    	
    	return $this->fetch();    
    }
    
    function project()
    {
    	$data=Action::live();
    	if($data[0]==0){throw \Exception($data[1]);}
    	$step=$_COOKIE['project']??1;
    	$res=Action::getproject($data[1]['uid'],(intval($step)-1)*20,20);
    	$this->assign(['data'=>$res[0],'num'=>$res[1]]);
    	return $this->fetch(); 
    }
    
    function allpj()
    {
    	$data=Action::live();
    	if($data[0]==0){throw \Exception($data[1]);}
    	if($data[1]['type']!='admin'){throw \Exception('Access Failed');}
    	$step=$_COOKIE['allpj']??1;
    	$data=Action::allpj((intval($step)-1)*20,20);
    	$this->assign(['data'=>$data[0],'num'=>$data[1]]);
    	return $this->fetch();
    }
    
    function userinfo()
    {
    	$data=Action::live();
    	if($data[0]==0){throw \Exception($data[1]);}
    	$this->assign(['uid'=>$data[1]['uid'],'name'=>$data[1]['name'],'sex'=>$data[1]['sex']==1?'男':'女','depa'=>$data[1]['depa'],'job'=>$data[1]['job'],'time'=>date('Y-m-d',$data[1]['time']),'phone'=>$data[1]['phone'],'email'=>$data[1]['email'],'pw'=>$data[1]['pw']]);
    	return $this->fetch(); 
    }
    
    public function user()
    {
    	$data=Action::live();
    	$data[0]!=0 or $this->error($data[1],'/');
    	$data[1]['type']=='user' or $this->redirect('/');
    	$this->assign(['logo'=>\think\config::get('info.logo'),'sign'=>\think\config::get('info.sign'),'Author'=>\think\config::get('info.Author'),'version'=>\think\config::get('info.version'),'company'=>\think\config::get('info.company')]);  	
    	return $this->fetch();
    }
    
    function admin()
    {
    	//think\Debug::remark('begin');  	
    	$data=Action::live();
    	$data[0]!=0 or $this->error($data[1],'/');
    	$data[1]['type']=='admin' or $this->redirect('/');
    	$this->assign(['logo'=>\think\config::get('info.logo'),'sign'=>\think\config::get('info.sign'),'Author'=>\think\config::get('info.Author'),'version'=>\think\config::get('info.version'),'company'=>\think\config::get('info.company')]);  	
    	//think\Debug::remark('end');  
		//echo think\Debug::getRangeTime('begin','end').'s','<br>',think\Debug::getRangeMem('begin','end');
    	return $this->fetch();
    }
    
    function userct($id)
    {
    	$data=Action::live();
    	if($data[0]==0){throw \Exception($data[1]);}
    	$res=Action::userct($data[1]['uid'],intval($id));
    	$this->assign(['project'=>$res[0],'series'=>$res[1],'year'=>$res[2],'id'=>intval($id)]);
    	return $this->fetch();
    }
     
    function logout()
    {
    	setcookie('usergl');setcookie('PHPSESSID');setcookie('engineer');setcookie('allpj');setcookie('project');
    	!\think\Session::has('USER') or \think\Session::delete('USER');
    	$this->success('Logout Success','/','',1);
    }
    
    function login()
    {
    	$_SERVER['REQUEST_METHOD']=='POST' or $this->error('post only!','/');
    	$user=$_POST['acount'];
    	$result = $this->validate(['acount'=>$user,'password'=>$_POST['password'],'csrf'=>$_POST['csrf']],'Index.login');
    	$result===true or $this->error($result,'/');
    	$res=Action::login($_POST['acount'],$_POST['password']);
    	if($res[0]===0){
    		$this->error($res[1]);
    	}else{
    		session_regenerate_id();//防止会话劫持
    		\think\Session::set("USER",$res[1]);
    		$this->success('Login Success','/'.$res[1]['type'],'',1);
    	}
    }
    
    function pid($id)
    {
    	$data=Action::live();
    	if($data[0]==0){throw \Exception($data[1]);}
    	if($data[1]['type']=='admin'){$data[1]['uid']='admin';}
    	preg_match("/^\w+$/",$id) or $this->error('Pid not Match');
    	$res=Action::pid($id,$data[1]['uid']);
    	if($res[0]===0){throw \Exception($res[1]);}
    	$this->assign(['res'=>$res[1],'list'=>$res[2]]);
    	return $this->fetch();
    }
    
    function uploadpid()
    {
    	$data=Action::live();
    	$data[0]!=0 or exit('{"statu":0,"msg":"'.$data[1].'"}');
    	$res=$this->validate(['pid'=>$_POST['pid'],'diff'=>$_POST['diff'],'day'=>$_POST['day'],'rater'=>$_POST['rate']],'Index.upload');
        $res===true or exit('{"statu":0,"msg":"'.$res.'"}');
        $res=Action::uppid($_POST['pid'],$_POST['day'],$_POST['rate'],$data,$_POST['diff']);
        exit('{"statu":"'.$res[0].'","msg":"'.$res[1].'"}');
    }
    
    function task($id)
    {
    	$data=Action::live();
    	if($data[0]==0){throw \Exception($data[1]);}
    	if($data[1]['type']=='admin'){$data[1]['uid']='admin';}
    	$res=Action::task($id,$data[1]['uid']);
    	if($res[0]===0){throw \Exception('Task Data Error');}
    	$this->assign(['res'=>$res[1]]);
    	return $this->fetch();
    }
    
    function newtask()
    {
    	$data=Action::live();
    	if($data[0]==0){throw \Exception($data[1]);}
    	if($data[1]['type']=='admin'){
    		$user=Action::getworker(0,-1);
    		$this->assign(['user'=>$user[0]]);
    	}else{
    		if($data[1]['pidpw']!=1){$this->error('Access Filed');}
    		$this->assign(['user'=>[['uid'=>$data[1]['uid'],'depa'=>$data[1]['depa'],'job'=>$data[1]['job'],'name'=>$data[1]['name']]]]);
    	}
    	return $this->fetch();	
    }
    
    function newpj()
    {
    	$data=Action::live();
    	if($data[0]==0){exit('{"statu":0,"msg":"'.$data[1].'"}');}
    	$_SERVER['REQUEST_METHOD']=='POST' or exit('{"statu":0,"msg":"Post only"}');
    	$res=$this->validate(['pjdata'=>$_POST['data']],'Index.newpj');
    	$res===true or exit('{"statu":0,"msg":"'.$res.'"}');
    	$pdata=json_decode($_POST['data'],true);
    	if($data[1]['type']=='admin'){
    		$res=Action::newpj($pdata,$data);
    	}else{
    		if($data[1]['pidpw']!=1){exit('{"statu":0,"msg":"Access Filed"}');}
    		if($data[1]['uid']!=$pdata[15]){exit('{"statu":0,"msg":"Enginner uid error"}');}
    		$res=Action::newpj($pdata,$data);
    	}
    	$res===true or exit('{"statu":0,"msg":"'.$res.'"}');
    	exit('{"statu":1,"msg":"success"}');
    }
    
    function engineer()
    {
    	$data=Action::live();
    	if($data[0]==0){throw \Exception($data[1]);}
    	if($data[1]['type']!='admin'){throw \Exception('Access Filed');}
    	$step=$_COOKIE['engineer']??1;
    	$data=Action::engineer((intval($step)-1)*20,20);
    	$this->assign(['user'=>$data[0],'num'=>$data[1]]);
    	return $this->fetch();
    }
    
    function worker($id)
    {
    	$data=Action::live();
    	if($data[0]==0){throw \Exception($data[1]);}
    	if($data[1]['type']!='admin'){throw \Exception('Access Filed');}
    	if(!preg_match("/^[0-9a-zA-Z]{6,12}+$/",$id)){throw \Exception('Uid not match');}
    	$data=Action::worker($id);
    	$this->assign(['data'=>$data[1],'name'=>$data[0],'year'=>$data[2],'uid'=>$id,'num'=>$data[3]]);
    	return $this->fetch();
    }
    
    function usergl()
    {
    	$data=Action::live();
    	if($data[0]==0){throw \Exception($data[1]);}
    	if($data[1]['type']!='admin'){throw \Exception('Access Filed');}
    	$step=$_COOKIE['usergl']??1;
    	$res=Action::getworker((intval($step)-1)*20,20);
    	$this->assign(['res'=>$res[0],'num'=>$res[1]]);
    	return $this->fetch();
    }
    
    function adduser()
    {
    	$data=Action::live();
    	if($data[0]==0){throw \Exception($data[1]);}
    	if($data[1]['type']!='admin'){throw \Exception('Access Filed');}
    	return $this->fetch();
    }
    
    function add()
    {
    	$data=Action::live();
    	if($data[0]==0){exit('{"statu":0,"msg":"'.$data[1].'"}');}
        $data[1]['type']=='admin' or exit('{"statu":0,"msg":"Access Filed"}');
    	$_SERVER['REQUEST_METHOD']=='POST' or exit('{"statu":0,"msg":"Post only"}');
    	$res=$this->validate(['newuser'=>$_POST['data']],'Index.add');
    	$res===true or exit('{"statu":0,"msg":"'.$res.'"}');
    	$res=Action::adduser($_POST['data'],$data);
    	$res===true or exit('{"statu":0,"msg":"'.$res.'"}');
    	exit('{"statu":1,"msg":"success"}');
    }
    
    function allct($id)
    {
    	$data=Action::live();
    	if($data[0]==0){throw \Exception($data[1]);}
    	$res=Action::allct(intval($id));
    	$this->assign(['project'=>$res[0],'series'=>$res[1],'year'=>$res[2],'id'=>intval($id)]);
    	return $this->fetch();
    }
    
    function ct()
    {
    	$data=Action::live();
    	if($data[0]==0){exit('{"statu":0,"msg":"'.$data[1].'"}');}
        $data[1]['type']=='admin' or exit('{"statu":0,"msg":"Access Filed"}');
    	$_SERVER['REQUEST_METHOD']=='POST' or exit('{"statu":0,"msg":"Post only"}');
    	$res=Action::ct($_POST['uid'],$_POST['data']);
    	if($res[0]==0){exit('{"statu":0,"msg":"'.$res[1].'"}');}
    	exit('{"statu":1,"msg":'.$res[1].',"series":'.$res[2].',"arr2":'.$res[3].'}');
    }
    
    function useredit($id)
    {
    	$data=Action::live();
    	if($data[0]==0){throw \Exception($data[1]);}
    	if($data[1]['type']=='user'){
    		$pdata=['name'=>$_POST['name'],'sex'=>$_POST['sex'],'depa'=>$_POST['depa'],'job'=>$_POST['job'],'time'=>$_POST['time'],'phone'=>$_POST['phone'],'email'=>$_POST['email'],'pwd'=>$_POST['pwd']];
    		$res=$this->validate(['uchg'=>$pdata],'Index.uchg');
    		$res===true or $this->error($res);
    		$res=Action::usave($pdata,$data);
    		if($res===true){
    			$this->success('Success');
    		}else{
    			$this->error($res);
    		}
    	}
    	if($data[1]['type']!='admin'){throw \Exception('Access Filed');}
    	$res=Action::useredit($id);
    	if($res[0]===0){throw \Exception($res[1]);}
    	$this->assign(['res'=>$res[1]]);
    	return $this->fetch();
    }
    
    function edsave()
    {
    	$data=Action::live();
    	if($data[0]==0){exit('{"statu":0,"msg":"'.$data[1].'"}');}
        $data[1]['type']=='admin' or exit('{"statu":0,"msg":"Access Filed"}');
    	$_SERVER['REQUEST_METHOD']=='POST' or exit('{"statu":0,"msg":"Post only"}');
    	$res=Action::edsave($_POST['uid'],$_POST['data'],$data);
    	if($res[0]==0){exit('{"statu":0,"msg":"'.$res[1].'"}');}
    	exit('{"statu":1,"msg":"success"}');
    }
    
    function jump()
    {
    	$data=Action::live();
    	if($data[0]==0){exit('{"statu":0,"msg":"'.$data[1].'"}');}
        $data[1]['type']=='admin' or exit('{"statu":0,"msg":"Access Filed"}');
    	$_SERVER['REQUEST_METHOD']=='POST' or exit('{"statu":0,"msg":"Post only"}');
    	$id=$_POST['id'];$step=intval($_POST['step']);$uid=isset($_POST['uid'])?$_POST['uid']:0;
    	preg_match("/^[0-9a-zA-Z]{1,12}+$/",$uid) or exit('{"statu":0,"msg":"Uid not Match"}');
    	in_array($id,['allpj','engineer','usergl','worker']) or exit('{"statu":0,"msg":"Page not exists"}');
    	$res=Action::adminjump($id,$step,$uid);
    	//$arr=json_encode([['id'=>2,'pid'=>'mt01378','custom'=>'中国馆','name'=>'测试6','type'=>'新建','engineer'=>'fonia','stime'=>'2019/9/9','etime'=>'2020/1/1','rtime'=>'','day'=>0,'offset'=>0,'rate'=>10],['id'=>1,'pid'=>'mt01378','custom'=>'中国馆','name'=>'测试6','type'=>'新建','engineer'=>'fonia','stime'=>'2019/9/9','etime'=>'2020/1/1','rtime'=>'','day'=>0,'offset'=>0,'rate'=>10]]);
    	exit('{"statu":1,"msg":"success","page":"'.$id.'","list":'.$res.'}');
    }
    
    function jumpb()
    {
    	$data=Action::live();
    	$data[0] or exit('{"statu":0,"msg":"'.$data[1].'"}');
    	$id=$_POST['id'];$step=intval($_POST['step']);
    	in_array($id,['project']) or exit('{"statu":0,"msg":"Page not exists"}');
    	$res=Action::userjump($data[1]['uid'],$id,$step);
    	exit('{"statu":1,"msg":"success","page":"'.$id.'","list":'.$res.'}');
    }
    
    function uploadsql()
    {
    	$live=Action::live();
    	if($live[0]==0){exit('{"statu":0,"msg":"'.$live[1].'"}');}
        $live[1]['type']=='admin' or exit('{"statu":0,"msg":"Access Filed"}');
        $_SERVER['REQUEST_METHOD']=='POST' or exit('{"statu":0,"msg":"Post only"}');
    	$data=json_decode($_POST['data'],true);$data['type']=intval($_POST['type']);
    	if($data['type']==1){
    		$res=$this->validate(['data'=>$data],'Index.uploadsql');
    	}else{
    		$res=$this->validate(['data'=>$data],'Index.uploadsql2');
    	}
    	$res===true or exit('{"statu":0,"msg":"'.$res.'"}');
    	$res=Action::upload($data['type'],$data,$live);
    	exit('{"statu":'.$res[0].',"msg":"'.$res[1].'"}');
    }
    
    function getwk()
    {
    	$data=Action::live();
    	if($data[0]==0){exit('{"statu":0,"msg":"'.$data[1].'"}');}
    	if($data[1]['type']=='admin'){
    	$user=Action::getworker(0,-1);
    	exit('{"statu":1,"list":'.json_encode($user[0]).'}');
    	}else{
    		$data[1]['pidpw']==1 or exit('{"statu":0,"msg":"Access Filed"}');
    		exit('{"statu":1,"list":'.json_encode([['uid'=>$data[1]['uid'],'depa'=>$data[1]['depa'],'job'=>$data[1]['job'],'name'=>$data[1]['name']]]).'}');
    	}
    }
    
    function uptask()
    {
    	$live=Action::live();
    	if($live[0]==0){exit('{"statu":0,"msg":"'.$live[1].'"}');}
    	if($live[1]['type']=='admin'||$live[1]['pidpw']==1){
    	$data=json_decode($_POST['data'],true);
    	$res=$this->validate(['uptask'=>$data,'pid'=>$_POST['pid']],'Index.uptask');
    	$res===true or exit('{"statu":0,"msg":"'.$res.'"}');
    	$res=Action::uptask($_POST['pid'],$data,$live);
    	exit('{"statu":1,"msg":"'.$res.'"}');
    	}else{
    		exit('{"statu":0,"msg":"Access Filed"}');
    	}
    }
    
    function rpw()
    {
    	$live=Action::live();
    	if($live[0]==0){exit('{"statu":0,"msg":"'.$live[1].'"}');}
    	$live[1]['type']=='admin' or exit('{"statu":0,"msg":"Access Filed"}');
    	if(!preg_match("/^[0-9a-zA-Z]{6,12}+$/",$_POST['uid'])){exit('{"statu":0,"msg":"Uid not match"}');}
    	$res=Action::rpw($_POST['uid'],$live);
    	$res[0]==1 or exit('{"statu":0,"msg":"'.$res[1].'"}');
    	exit('{"statu":1,"msg":"'.$res[1].'"}');
    }
    
    function analyze($id)
    {
    	$live=Action::live();
    	if($live[0]==0){throw \Exception($live[1]);}
    	$live[1]['type']=='admin' or $this->error('Access Filed','/allpj','',2);
    	if(!preg_match("/^[0-9a-zA-Z_]{1,20}+$/",$id)){$this->error('Uid not match','/allpj','',2);}
    	$data=Action::analyze($id);
    	$this->assign(['id'=>$id,'data'=>$data]);
    	return $this->fetch();
    }
}
