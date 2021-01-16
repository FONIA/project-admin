<?php
namespace fonia;
use \think\Db;
use \think\Session;
use think\Debug;
class ac{
	static function live()
	{
		$data=Session::get('USER');
		if(empty($data)){return [0,'Session is Overtime , Please Login again'];}
		if(\think\Request::instance()->ip()!==$data['ip']){Session::delete('USER');return [0,'IP is Change , Please Login again'];}
		$res=Db::query('select uid,name,sex,depa,job,time,phone,email,passw,type,pw,pidpw from user where uid=? limit 1',[$data['uid']]);
		if(empty($res)){Session::delete('USER');return [0,'Acount is Empty , Please Login again'];}
		if($res[0]['passw']!==$data['passw']){Session::delete('USER');return [0,'Password is Change , Please Login again'];}
		Session::set("USER",['uid'=>$res[0]['uid'],'ip'=>$data['ip'],'name'=>$res[0]['name'],'sex'=>$res[0]['sex'],'depa'=>$res[0]['depa'],'job'=>$res[0]['job'],'time'=>$res[0]['time'],'phone'=>$res[0]['phone'],'email'=>$res[0]['email'],'passw'=>$res[0]['passw'],'type'=>$res[0]['type'],'pw'=>$res[0]['pw'],'pidpw'=>$res[0]['pidpw']]);
        return [1,['uid'=>$res[0]['uid'],'ip'=>$data['ip'],'name'=>$res[0]['name'],'sex'=>$res[0]['sex'],'depa'=>$res[0]['depa'],'job'=>$res[0]['job'],'time'=>$res[0]['time'],'phone'=>$res[0]['phone'],'email'=>$res[0]['email'],'passw'=>$res[0]['passw'],'type'=>$res[0]['type'],'pw'=>$res[0]['pw'],'pidpw'=>$res[0]['pidpw']]];
	}
	
	static function login_write($data)
	{
		$sql='insert into login(uid,statu,content,ip,time) values("'.$data['uid'].'","'.$data['statu'].'","'.$data['content'].'","'.(\think\Request::instance()->ip()).'","'.time().'")';
		Db::startTrans();
		try{
			Db::execute($sql);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
		}
	}
	
	static function login($acount,$pass)
	{
		$res=Db::query('select uid,name,sex,depa,job,time,phone,email,passw,type,pw,pidpw from user where acount=? limit 1',[$acount]);
		if(!(bool)$res){self::login_write(['content'=>'账号不存在，登录密码：'.$pass,'statu'=>0,'uid'=>$acount]);return [0,'Acount not activated or exist'];}
		if(!password_verify($pass,$res[0]['passw'])){self::login_write(['content'=>'密码错误，登录密码：'.$pass,'statu'=>0,'uid'=>$acount]);return [0,'Acount or Password not match'];}
		self::login_write(['content'=>'登录成功','statu'=>1,'uid'=>$acount]);
		return [1,['uid'=>$res[0]['uid'],'ip'=>\think\Request::instance()->ip(),'name'=>$res[0]['name'],'sex'=>$res[0]['sex'],'depa'=>$res[0]['depa'],'job'=>$res[0]['job'],'time'=>$res[0]['time'],'phone'=>$res[0]['phone'],'email'=>$res[0]['email'],'passw'=>$res[0]['passw'],'type'=>$res[0]['type'],'pw'=>$res[0]['pw'],'pidpw'=>$res[0]['pidpw']]];
	}
	
	static function getproject($uid,$current=0,$offset=20)
	{
		if($current<0){$current=0;}
		$sql='select id,pid,custom,name,type,(select name from user where uid="'.$uid.'" limit 1) as worker,FROM_UNIXTIME(stime,"%Y/%m/%d") stime,FROM_UNIXTIME(etime,"%Y/%m/%d") etime,(select rate from rate where pid=s.pid limit 1) as rate from project s where worker="'.$uid.'" order by stime desc limit '.$current.','.$offset;
		$res=Db::query($sql);
		foreach($res as &$k){
			$k['rate']=round(array_sum(json_decode($k['rate'],true))/21,2);
		}
		$num=Db::query('select count(*) as num from project where worker="'.$uid.'"');
		return [$res,ceil($num[0]['num']/$offset)];
	}
	
	static function pid($pid,$uid)
	{
		if($uid=='admin'){
			$sql='select stime,etime,custom,(select name from user where uid=a.worker limit 1) as wk,ext from project a where pid="'.$pid.'" limit 1';		
		}else{
			$sql='select stime,etime,custom,(select name from user where uid="'.$uid.'" limit 1) as wk,ext from project where pid="'.$pid.'" and worker="'.$uid.'" limit 1';	
		}
		$sql2='select rate,time,diff from rate where pid="'.$pid.'"';
		$res=Db::query($sql);$res2=Db::query($sql2);
		if(empty($res)||empty($res2)){return [0,'Data Config Error or Try again after a moment'];}
		if($res[0]['ext']===null){$num=$kind='N/A';}else{
			$t=json_decode(stripslashes($res[0]['ext']),true);
			$num=$t[1];$kind=$t[0];
		}
		$mb=json_decode(file_get_contents(ROOT_PATH.'database/mb/1.json'),true);	
		$data=['pid'=>$pid,'stime'=>date('Y/m/d',$res[0]['stime']),'etime'=>date('Y/m/d',$res[0]['etime']),'worker'=>$res[0]['wk'],'custom'=>$res[0]['custom'],'num'=>$num,'kind'=>$kind];
		$rate=json_decode(stripslashes($res2[0]['rate']),true);
		$time=json_decode(stripslashes($res2[0]['time']),true);
		$diff=json_decode(stripslashes($res2[0]['diff']),true);
		$arr=[];
		foreach($mb as $v=>$k){
			array_push($arr,[$k,@$rate[$v],@$time[$v],@$diff[$v]]);
		}
		return [1,$data,$arr];
	}
	
	static function uppid($pid,$day,$rate,$user,$diff)
	{
		if($user[1]['type']=='admin'){
			$res=Db::query('select pid from project where pid=?',[$pid]);
		}else{
			$res=Db::query('select pid from project where pid=? and worker=?',[$pid,$user[1]['uid']]);
		}
		if(!(bool)$res){return [0,'The project is empty'];}		
		$day=json_decode($day,true);
		$rate=json_decode($rate,true);
		$diff=json_decode($diff,true);
		foreach($day as &$k){
			$k=intval($k);
		}
		foreach($rate as &$v){
			$v=round($v,1);
		}
		foreach($diff as &$s){
			$s=intval($s);
		}
		$content=['diff'=>$day,'rate'=>$rate,'time'=>$diff];
		$day=addslashes(json_encode($day));
		$rate=addslashes(json_encode($rate));
		$diff=addslashes(json_encode($diff));
		$sql='update `rate` set time="'.$diff.'",diff="'.$day.'",rate="'.$rate.'" where pid="'.$pid.'" limit 1';
		$sql2='insert into log(uid,pid,time,type,ip,content) values("'.$user[1]['uid'].'","'.$pid.'","'.time().'","项目进度编辑","'.$user[1]['ip'].'","'.addslashes(json_encode($content)).'")';				
		Db::startTrans();
		try{
			Db::execute($sql);
			Db::execute($sql2);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return [0,'System is busy , Try again after a moment'];
		}
		return [1,'success'];
	}
	
	static function task($pid,$uid)
	{
		if($uid=='admin'){
			$sql='select type,custom,name,stime,etime,(select name from user where uid=a.worker limit 1) as worker,ext,ask from project a where pid="'.$pid.'"';		
		}else{
			$sql='select type,custom,name,stime,etime,(select name from user where uid="'.$uid.'" limit 1) as worker,ext,ask from project where pid="'.$pid.'" and worker="'.$uid.'"';	
		}
		$res=Db::query($sql);
		if(empty($res)){return [0,'The project is empty'];}	
		$ext=json_decode(stripslashes($res[0]['ask']),true);
		$ask=json_decode(stripslashes($res[0]['ext']),true);
		return [1,['pid'=>$pid,'name'=>$res[0]['name'],'custom'=>$res[0]['custom'],'type'=>$res[0]['type'],'stime'=>$res[0]['stime'],'etime'=>$res[0]['etime'],'worker'=>$res[0]['worker'],'ext'=>$ext,'ask'=>$ask]];
	}
	
	static function userct($uid,$m)
	{
		$sql='select distinct type from project where worker="'.$uid.'" and date_format(from_unixtime(stime),"%Y")="'.$m.'"';
		$sql2='select type,count(type) as num,date_format(from_unixtime(stime),"%c") as time from project where worker="'.$uid.'" and date_format(from_unixtime(stime),"%Y")="'.$m.'" group by date_format(from_unixtime(stime),"%c"),type order by date_format(from_unixtime(stime),"%c") asc';
		$sql3='select date_format(from_unixtime(stime),"%Y") as year from project where worker="'.$uid.'" group by date_format(from_unixtime(stime),"%Y")';
		$res=Db::query($sql);$res2=Db::query($sql2);$res3=Db::query($sql3);$series=[];
		$type=['product'];$times=[['1月份'],['2月份'],['3月份'],['4月份'],['5月份'],['6月份'],['7月份'],['8月份'],['9月份'],['10月份'],['11月份'],['12月份']];
		foreach($res as $i=>$s){
			foreach($times as &$p){
				//$p[0]=urlencode($p[0]);
				$p[$i+1]=0;
			}
	    }
		foreach($res as $x=>$k){
			array_push($series,"{type: 'bar',stack:'项目',label: {show: true,position: 'inside'}}");
			array_push($type,urlencode($k['type']));
			foreach($res2 as $v){
			if($k['type']==$v['type']){
				array_splice($times[$v['time']-1],$x+1,1,$v['num']);
			}
		    }
		}
		$arr=array_merge([$type],$times);
		$arr=urldecode(json_encode($arr));
		return [$arr,implode(',',$series),$res3];
	}
	
	static function allpj($current=0,$offset=20)
	{
		if($current<0){$current=0;}
		$sql='select id,pid,custom,name,type,(select name from user where uid=a.worker) as worker,stime,etime,(select time from rate where pid=a.pid) as diff,(select diff from rate where pid=a.pid) as rtime,(select rate from rate where pid=a.pid) as rate from project a order by id desc limit '.$current.','.$offset;
		$res=Db::query($sql);
		$num=Db::query('select count(*) as num from project where 1');
		foreach($res as &$k){
			$k['diff']=array_sum(json_decode(stripslashes($k['diff']),true));
			$k['rate']=round((array_sum(json_decode(stripslashes($k['rate']),true))/count(json_decode(stripslashes($k['rate']),true))),2);
			if($k['rate']==100){$k['rtime']=date("Y/m/d",$k['stime']+array_sum(json_decode(stripslashes($k['rtime']),true))*86400);}else{$k['rtime']='';}
			$k['stime']=date("Y/m/d",$k['stime']);
			$k['etime']=date("Y/m/d",$k['etime']);
			$k['day']=ceil((strtotime($k['etime'])-strtotime($k['stime']))/86400)+1;
		}
		return [$res,ceil($num[0]['num']/$offset)];
	}
	
	static function getworker($current=0,$offset=20)
	{
		if($current<0){$current=0;}
		$num=Db::query('select count(*) as num from user where type="user"');
		if($offset==-1){$offset=$num[0]['num'];}
		$sql='select id,uid,depa,job,name,date_format(from_unixtime(time),"%Y/%m/%d") time,phone,email,statu,if(sex=1,"男","女") sex from user where type="user" order by id asc limit '.$current.','.$offset;
		$res=Db::query($sql);
		return [$res,ceil($num[0]['num']/$offset)];
	}
		
	
	static function newpj($data,$user)
	{		
		$res=Db::query('select pid from project where pid=? limit 1',[$data[0]]);
		if((bool)$res){return 'Pid is exist , change a pid';}
		$res=Db::query('select uid from user where uid=? limit 1',[$data[15]]);
		if(!(bool)$res){return 'Engineer not exist';}
		$ext=addslashes(json_encode(array_merge(array_slice($data,4,3),array_slice($data,9,6))));
		$ask=addslashes(json_encode(array_slice($data,16,5)));
		$sql='insert into project(pid,type,custom,name,addtime,stime,etime,worker,ext,ask) values("'.$data[0].'","'.$data[2].'","'.$data[3].'","'.$data[1].'","'.time().'","'.strtotime($data[7]).'","'.strtotime($data[8]).'","'.$data[15].'","'.$ext.'","'.$ask.'")';
		$sql2='insert into rate(pid,rate,time,diff) values("'.$data[0].'","[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]","[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]","[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]")';
		$sql3='insert into log(uid,pid,time,type,ip,content) values("'.$user[1]['uid'].'","'.$data[0].'","'.time().'","新建项目","'.$user[1]['ip'].'","'.addslashes(json_encode($data)).'")';
		Db::startTrans();
		try{
			Db::execute($sql);
			Db::execute($sql2);
			Db::execute($sql3);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return 'System is busy , Try again after a moment';
		}
		return true;
	}
	
	static function engineer($current=0,$offset=20)
	{
		if($current<0){$current=0;}
		$sql='select id,uid,name,depa,job,(0) as num from user a where type="user" order by id asc limit '.$current.','.$offset;
		$res=Db::query($sql);
		$pnum=Db::query('select count(*) as num from user where type="user"');
		foreach($res as &$k){
			$sql2='select a.rate from rate a,project b where b.worker="'.$k["uid"].'" and b.pid=a.pid';
			$res2=Db::query($sql2);$num=0;
			foreach($res2 as $v){
				$v['rate']=round((array_sum(json_decode(stripslashes($v['rate']),true))/count(json_decode(stripslashes($v['rate']),true))),2);
				if($v['rate']<100){$num++;}else{
					$k['num']++;
				}
			}
			$k['rnum']=$num;
		}
		return [$res,ceil($pnum[0]['num']/$offset)];
	}
	
	static function worker($id,$current=0,$offset=20)
	{
		if($current<0){$current=0;}
		$sql='select name from user where uid="'.$id.'" limit 1';
		$sql2='select id,pid,type,custom,name,date_format(from_unixtime(stime),"%Y/%m/%d") stime,date_format(from_unixtime(etime),"%Y/%m/%d") etime,(select rate from rate where pid=a.pid limit 1) as rate,(select time from rate where pid=a.pid limit 1) as time from project a where worker="'.$id.'" order by id desc limit '.$current.','.$offset;
		$sql3='select distinct date_format(from_unixtime(stime),"%Y") year from project where 1';
		$num=Db::query('select count(*) as num from project where worker="'.$id.'"');
		$res=Db::query($sql);
		if(empty($res)){return [null,null];}
		$res2=Db::query($sql2);$res3=Db::query($sql3);
		foreach($res2 as &$k){
			$k['rate']=round(array_sum(json_decode(stripslashes($k['rate']),true))/21,2);
			$k['time']=array_sum(json_decode(stripslashes($k['time']),true));
			$k['day']=ceil((strtotime($k['etime'])-strtotime($k['stime']))/86400)+1;
		}
		return [$res[0]['name'],$res2,$res3,ceil($num[0]['num']/$offset)];
	}
	
	static function adduser($data,$user)
	{
		$data=json_decode($data,true);
		$sql='select id from user where uid="'.$data[0].'" limit 1';		
		$sql2='insert into user(uid,name,sex,depa,job,time,phone,email,statu,leader,ext,addtime,acount,passw,type) values("'.$data[0].'","'.addslashes($data[1]).'",'.($data[2]=='男'?1:0).',"'.addslashes($data[3]).'","'.addslashes($data[4]).'","'.strtotime($data[5]).'","'.$data[6].'","'.addslashes($data[7]).'","'.addslashes($data[8]).'","'.addslashes($data[9]).'","'.addslashes($data[10]).'","'.time().'","'.$data[0].'","'.password_hash($data[0],PASSWORD_DEFAULT).'","user")';
		$sql3='insert into log(uid,pid,time,type,ip,content) values("'.$user[1]['uid'].'","null","'.time().'","新建员工","'.$user[1]['ip'].'","'.addslashes(json_encode($data)).'")';	
		$res=Db::query($sql);
		if((bool)$res){return 'Uid is exists';}
		Db::startTrans();
		try{
			Db::execute($sql2);
			Db::execute($sql3);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return 'System is busy , Try again after a moment';
		}
		return true;
	}
	
	static function allct($m)
	{
		$sql='select distinct type from project where date_format(from_unixtime(stime),"%Y")="'.$m.'"';
		$sql2='select type,count(type) as num,date_format(from_unixtime(stime),"%c") as time from project where date_format(from_unixtime(stime),"%Y")="'.$m.'" group by date_format(from_unixtime(stime),"%c"),type order by date_format(from_unixtime(stime),"%c") asc';
		$sql3='select date_format(from_unixtime(stime),"%Y") as year from project where 1 group by date_format(from_unixtime(stime),"%Y")';
		$res=Db::query($sql);$res2=Db::query($sql2);$res3=Db::query($sql3);$series=[];
		$type=['product'];$times=[['1月份'],['2月份'],['3月份'],['4月份'],['5月份'],['6月份'],['7月份'],['8月份'],['9月份'],['10月份'],['11月份'],['12月份']];
		foreach($res as $i=>$s){
			foreach($times as &$p){
				//$p[0]=urlencode($p[0]);
				$p[$i+1]=0;
			}
	    }
		foreach($res as $x=>$k){
			array_push($series,"{type: 'bar',stack:'项目',label: {show: true,position: 'inside'}}");
			array_push($type,urlencode($k['type']));
			foreach($res2 as $v){
			if($k['type']==$v['type']){
				array_splice($times[$v['time']-1],$x+1,1,$v['num']);
			}
		    }
		}
		$arr=array_merge([$type],$times);
		$arr=urldecode(json_encode($arr));
		return [$arr,implode(',',$series),$res3];
	}
	
	static function ct($uid,$data)
	{
		$data=json_decode($data,true);
		$sql='select id from user where uid="'.$uid.'" limit 1';
		$res=Db::query($sql);
		if(empty($res)){return [0,'worker not exists'];}
		$sql2='select name,pid,stime,(select diff from rate where pid=a.pid limit 1) time from project a where worker="'.$uid.'" and date_format(from_unixtime(stime),"%Y")='.intval($data[0]).' and date_format(from_unixtime(stime),"%c")='.intval($data[1]);
		$res2=Db::query($sql2);$arr=[[],[]];$time=[];$arr2=[];
		foreach($res2 as $k){
			array_push($time,[]);
			array_push($arr[0],$k['name'].'('.$k['pid'].')');
			array_push($arr[1],[$k['stime'],$k['time']]);
		}
		foreach($arr[1] as $z=>$v){
			$v[1]=array_sum(json_decode($v[1],true));
			$v[0]=strtotime(date("Y/m/d",$v[0]));
			array_push($time[$z],$v[0]);
			for($y=1;$y<=$v[1];$y++){
				array_push($time[$z],end($time[$z])+86400);
			}
			$time[$z]=array_unique($time[$z]);
			sort($time[$z]);$arr2[$z]=$time[$z];
			$time[$z]=array_map(function($a){return date("Y/n/j",$a);},$time[$z]);
		}
		$series=$arr[1];
		foreach($series as &$i){
			$i[1]=json_decode($i[1],true);
		}
        foreach($time as &$t){
        	$t=implode(',',$t);
        }              	
		$time=array_unique(explode(',',implode(',',$time)));
		return [1,json_encode(['date'=>implode(',',$time),'data'=>$arr[0]]),json_encode($series),json_encode($arr2)];
	}
	
	static function useredit($id)
	{
		$sql='select uid,name,if(sex,"男","女") sex,depa,job,time,phone,email,statu,leader,ext from user where uid="'.$id.'" limit 1';
		$res=Db::query($sql);
		if(!(bool)$res){return [0,'Uid not exist'];}
		return [1,$res[0]];
	}
	
	static function edsave($uid,$data,$user)
	{
		$data=json_decode($data,true);
		$sql='select uid from user where uid="'.$uid.'" limit 1';
		$sql2='update user set name="'.addslashes($data[1]).'",sex='.($data[2]=='男'?1:0).',depa="'.addslashes($data[3]).'",job="'.addslashes($data[4]).'",time="'.strtotime($data[5]).'",phone="'.$data[6].'",email="'.addslashes($data[7]).'",statu="'.addslashes($data[8]).'",leader="'.addslashes($data[9]).'",ext="'.addslashes($data[10]).'" where uid="'.$uid.'" limit 1';
		$sql3='insert into log(uid,pid,time,type,ip,content) values("'.$user[1]['uid'].'","null","'.time().'","员工信息修改","'.$user[1]['ip'].'","'.addslashes(json_encode($data)).'")';		
		$res=Db::query($sql);
		if(!(bool)$res){return [0,'Uid not exist'];}
		Db::startTrans();
		try{
			Db::execute($sql2);
			Db::execute($sql3);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return [0,'System is busy , Try again after a moment'];
		}
		return [1,1];
	}
	
	static function usave($data,$user)
	{
		$sql='select uid,pw from user where uid="'.$user[1]['uid'].'" limit 1';
		$res=Db::query($sql);
		if(!(bool)$res){return 'Uid not exist';}
		if($res[0]['pw']<=0){return '重置次数超额！联系管理员解决！';}
		$sql2='update user set name="'.addslashes($data['name']).'",sex='.($data['sex']=='男'?1:0).',depa="'.addslashes($data['depa']).'",job="'.addslashes($data['job']).'",time="'.strtotime($data['time']).'",phone="'.$data['phone'].'",email="'.addslashes($data['email']).'",passw="'.password_hash($data['pwd'],PASSWORD_DEFAULT).'",pw='.($res[0]['pw']-1).' where uid="'.$user[1]['uid'].'" limit 1';		
		$sql3='insert into log(uid,pid,time,type,ip,content) values("'.$user[1]['uid'].'","null","'.time().'","用户信息修改","'.$user[1]['ip'].'","'.addslashes(json_encode($data)).'")';
		Db::startTrans();
		try{
			Db::execute($sql2);
			Db::execute($sql3);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return 'System is busy , Try again after a moment'.$e;
		}
		return true;
	}
	
	static function adminjump($id,$step,$uid){
		switch($id){
			case 'allpj':
				$data=self::allpj(($step-1)*20,20);
				return json_encode($data[0]);
			break;
			case 'engineer':
				$data=self::engineer(($step-1)*20,20);
				return json_encode($data[0]);
			break;
			case 'usergl':
				$data=self::getworker(($step-1)*20,20);
				return json_encode($data[0]);
			break;
			case 'worker':
				$data=self::worker($uid,($step-1)*20,20);
				return json_encode($data[1]);
			break;
			default:break;
		}
	}
	
	static function userjump($uid,$id,$step)
	{
		switch($id){
			case 'project':
				$data=self::getproject($uid,($step-1)*20,20);
				return json_encode($data[0]);
			break;
		}
	}
	
	static function upload($type,$data,$user)
	{
		
		array_pop($data);
		if($type==1){
		$num=array_column($data,0);
		if(count($num)!=count(array_unique($num))){return [0,'Uid repeat'];}
		foreach($data as $v){	
			$res=Db::query('select uid from user where uid=? limit 1',[$v[0]]);
			if((bool)$res){return [0,'Uid '.$v[0].' is exists'];}
		}
		foreach($data as &$k){
			$k=['uid'=>$k[0],'name'=>$k[1],'sex'=>($k[2]=='男'?1:0),'depa'=>$k[3],'job'=>$k[4],'phone'=>$k[6],'email'=>$k[7],'time'=>intval($k[5]),'statu'=>$k[8],'leader'=>$k[9],'ext'=>$k[10],'addtime'=>time(),'acount'=>$k[0],'passw'=>password_hash($k[0],PASSWORD_DEFAULT),'type'=>'user'];
		}
		$db='user';
		}else{
		$num=array_column($data,0);
		if(count($num)!=count(array_unique($num))){return [0,'Pid repeat'];}
		$rate=[];
		foreach($data as $v){
			$res=Db::query('select pid from project where pid=? limit 1',[$v[0]]);
			if((bool)$res){return [0,'Pid '.$v[0].' is exists'];}
			$res2=Db::query('select uid from user where uid=? limit 1',[$v[15]]);
			if(!(bool)$res2){return [0,'Engineer not exist , uid:'.$v[15].''];}
		}
		foreach($data as &$k){
			$ext=addslashes(json_encode(array_merge(array_slice($k,4,3),array_slice($k,9,6))));
			$ask=addslashes(json_encode(array_slice($k,16,5)));
			array_push($rate,['pid'=>$k[0],'rate'=>"[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]",'time'=>"[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]",'diff'=>"[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]"]);
			$k=['pid'=>$k[0],'type'=>$k[2],'custom'=>$k[3],'name'=>$k[1],'addtime'=>time(),'stime'=>intval($k[7]),'etime'=>intval($k[8]),'worker'=>$k[15],'ext'=>$ext,'ask'=>$ask];
		}
		$db='project';
		}
		$sql2='insert into log(uid,pid,time,type,ip,content) values("'.$user[1]['uid'].'","null","'.time().'","'.$db.'数据批量录入","'.$user[1]['ip'].'","'.addslashes(json_encode($data)).'")';				
		Db::startTrans();
		try{
			//Db::execute($sql2);
			Db::name($db)->insertAll($data);
			Db::execute($sql2);
			if($type==0){
				Db::name('rate')->insertAll($rate);
			}
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return [0,'System is busy , Try again after a moment'.$e];
		}
		return [1,'success'];
	}
	
	static function uptask($pid,$data,$user)
	{
		$res=Db::query('select pid from project where pid=? limit 1',[$pid]);
		if(empty($res)){return 'Project not exist';}
		$res=Db::query('select uid from user where uid=? limit 1',[$data[14]]);
		if(empty($res)){return 'Engineer not exist';}
		$ext=addslashes(json_encode(array_merge(array_slice($data,3,3),array_slice($data,8,6))));
		$ask=addslashes(json_encode(array_slice($data,15,5)));
		$sql='update project set type="'.$data[1].'",custom="'.$data[2].'",name="'.$data[0].'",worker="'.$data[14].'",stime="'.strtotime($data[6]).'",etime="'.strtotime($data[7]).'",ext="'.$ext.'",ask="'.$ask.'" where pid="'.$pid.'" limit 1';
		$sql2='insert into log(uid,pid,time,type,ip,content) values("'.$user[1]['uid'].'","'.$pid.'","'.time().'","修改项目资料","'.$user[1]['ip'].'","'.addslashes(json_encode($data)).'")';		
		Db::startTrans();
		try{
			Db::execute($sql);
			Db::execute($sql2);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return 'System is busy , Try again after a moment';
		}
		return 'success';
	}
	
	static function rpw($uid,$user)
	{
		$res=Db::query('select uid from user where uid=? limit 1',[$uid]);
		if(!(bool)$res){return [0,'Uid not exists'];}
		$sql='update user set passw="'.password_hash($uid,PASSWORD_DEFAULT).'",pw=1 where uid="'.$uid.'" limit 1';
		$sql2='insert into log(uid,pid,time,type,ip,content) values("'.$user[1]['uid'].'","null","'.time().'","密码重置","'.$user[1]['ip'].'","账号：'.$uid.'")';		
		Db::startTrans();
		try{
			Db::execute($sql);
			Db::execute($sql2);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return [0,'System is busy , Try again after a moment'];
		}
		return [1,'success'];
	}
	
	static function analyze($id)
	{
		$sql='select time,content from log where pid="'.$id.'" and type="项目进度编辑" order by time desc';
		$res=Db::query($sql);$m=[];
		foreach($res as $i=>&$k){
			if(in_array(date('Y/m/d',$k['time']),$m)){unset($res[$i]);}else{
			$k[0]=date('Y/m/d',$k['time']);
			$k[1]=json_decode(stripslashes($k['content']),true)['rate'];
			array_push($m,date('Y/m/d',$k['time']));unset($k['time']);unset($k['content']);
			}
		}
		$res=array_reverse($res);
		return json_encode($res);
	}
}
?>