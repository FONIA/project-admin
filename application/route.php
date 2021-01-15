<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//use think\Route;
return [
'login'=>['index/index/login','post'],
'logout'=>['index/index/logout','get'],
'user'=>['index/index/user','get'],
'admin'=>['index/index/admin','get'],
'allpj'=>['index/index/allpj','get'],
'pid/:id'=>['index/index/pid','get'],
'project'=>['index/index/project','get'],
'userinfo'=>['index/index/userinfo','get'],
'userct/:id'=>['index/index/userct','get'],
'allct/:id'=>['index/index/allct','get'],
'newtask'=>['index/index/newtask','get'],
'newpj'=>['index/index/newpj','post'],
'adduser'=>['index/index/adduser','get'],
'worker/:id'=>['index/index/worker','get'],
'engineer'=>['index/index/engineer','get'],
'usergl'=>['index/index/usergl','get'],
'adduser'=>['index/index/adduser','get'],
'task/:id'=>['index/index/task','get'],
'uploadpid'=>['index/index/uploadpid','post'],
'add'=>['index/index/add','post'],
'ct'=>['index/index/ct','post'],
'edsave'=>['index/index/edsave','post'],
'useredit/:id'=>['index/index/useredit','get'],
'jump'=>['index/index/jump','post'],
'jumpb'=>['index/index/jumpb','post'],
'gtexcel'=>['index/Excel/gtexcel','get|post'],
'batch'=>['index/Excel/index','get'],
'uploadsql'=>['index/index/uploadsql','post'],
'getwk'=>['index/index/getwk','post'],
'uptask'=>['index/index/uptask','post'],
'rpw'=>['index/index/rpw','post'],
'analyze/:id'=>['index/index/analyze','get'],
];
