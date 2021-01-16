"use strict";
var FONIA = (function() {
	const config = [
		[65, 117, 116, 104, 111, 114, 58, 70, 79, 78, 73, 65, 32, 44, 32, 87, 120, 58, 68, 67, 49, 51, 53, 54, 48, 10, 81, 50, 57, 117, 90, 51, 74, 104, 100, 72, 86, 115, 89, 88, 82, 112, 98, 50, 53, 122, 76, 67, 66, 112, 100, 67, 100, 122, 73, 71, 70, 117, 73, 71, 86, 110, 90, 119, 61, 61],
		[36134, 21495, 12289, 23494, 30721, 54, 45, 49, 50, 20301, 38271, 24230, 40, 23383, 27597, 12289, 25968, 23383, 12289, 19979, 21010, 32447, 41],
		[94, 91, 97, 45, 122, 65, 45, 90, 48, 45, 57, 95, 93, 123, 54, 44, 49, 50, 125, 36],
		[39033, 30446, 32534, 21495, 21482, 21253, 21547, 65306, 23383, 27597, 12289, 25968, 23383, 12289, 19979, 21010, 32447, 65281],
		[39033, 30446, 31867, 22411, 19981, 33021, 20026, 31354, 65281],
		[23458, 25143, 19981, 33021, 20026, 31354, 65281],
		[39033, 30446, 21517, 31216, 19981, 33021, 20026, 31354, 65281],
		[35831, 36873, 25321, 39033, 30446, 24037, 31243, 24072, 65281],
		[39033, 30446, 26102, 38388, 19981, 33021, 20026, 31354, 65281],
		[39033, 30446, 20132, 20184, 26102, 38388, 19981, 21512, 29702, 65281],
		[35813, 38454, 27573, 24050, 23436, 25104, 49, 48, 48, 37, 65292, 26080, 27861, 20877, 30003, 25253, 65281],
		[36127, 36131, 20154, 19981, 33021, 20026, 31354, 65281],
		[24320, 22987, 26102, 38388, 19981, 33021, 20026, 31354, 65281],
		[35745, 21010, 23436, 25104, 26102, 38388, 19981, 33021, 20026, 31354, 65281],
		[23436, 25104, 36827, 24230, 19981, 33021, 20026, 31354, 65281],
		[30003, 25253, 23436, 25104, 36827, 24230, 19981, 33021, 23567, 20110, 24050, 23436, 25104, 36827, 24230, 65281],
		 [24403, 21069, 24050, 23436, 25104, 65306]
	].map(x => Array.from(x, x => String.fromCharCode(x)).join(''));
	window.addEventListener('resize', window.author = () => {
		console.clear();console.log(config[0]);
		window.removeEventListener('resize', window.author);
	});
	return {
		RegExp: function() {
			return(new RegExp(config[2])).test(arguments[0]);
		},
		USER: function() {
			var live = document.getElementsByClassName('live');
			for(var i = 0; i < live.length; i++) {
				live[i].innerHTML = live[i].innerText + '%' + '<span style="width:' + live[i].innerText + '%;"></span><br>';
			}
			setInterval(function() {
				var o = document.getElementById('time');
				var n = new Date()
				o.innerText = n.getFullYear() + '-' + n.getMonth() + '-' + n.getDate() + ' ' + n.getHours() + ':' + n.getMinutes() + ':' + n.getSeconds();
			}, 1000);
		},
		NewPid: function() {
			var c = ['id', 'type', 'custom', 'title', 'worker', 'stime', 'etime', 'tips'].map(x => {
				return document.getElementsByName(x)[0].value;
			});
			if(!/^\w+$/.test(c[0])) {
				alert(config[3]);
				return false;
			}
			if(c[1] == '') {
				alert(config[4]);
				return false;
			}
			if(c[2] == '') {
				alert(config[5]);
				return false;
			}
			if(c[3] == '') {
				alert(config[6]);
				return false;
			}
			if(c[4] == 'null') {
				alert(config[7]);
				return false;
			}
			if(c[5].length <= 0 || c[6].length <= 0) {
				alert(config[8]);
				return false;
			}
			var s = new Date(c[5]);
			var e = new Date(c[6]);
			if(s > e) {
				alert(config[9]);
				return false;
			}
			return true;
		},
		Pid: function() {
			var live = document.getElementsByClassName('live');
			var diff = document.getElementsByClassName('diff');
			var arr = new Array();
			var sum = 0;
			for(var i = 0; i < live.length; i++) {
				live[i].innerHTML = live[i].innerText + '%' + '<span style="width:' + live[i].innerText + '%;"></span><br>';
				arr.push(diff[i].innerText);
			}
			for(var i = 0; i < arr.length; i++) {
				sum += parseFloat(arr[i]);
				if(i == 5) {
					document.getElementById('a').innerHTML = sum;
					sum = 0;
				}
				if(i == 17) {
					document.getElementById('c').innerHTML = sum;
					sum = 0;
				}
				if(i == 19) {
					document.getElementById('b').innerHTML = sum;
					sum = 0;
				}
				if(i == 20) {
					document.getElementById('d').innerHTML = sum;
					sum = 0;
				}
			}
			var td = document.getElementsByTagName("td");
			for(var i = 0; i < td.length; i++) {
				td[i].onmouseover = function() {
					this.style.backgroundColor = "#88caff";
				}
				td[i].onmouseout = function() {
					this.removeAttribute("style");
				}
			}
			document.getElementById('mask').addEventListener('click', function() {
				this.removeAttribute('class');
				var t = document.getElementById('tbody').getElementsByTagName('tr');
				for(var i = 0; i < t.length; i++) {
					t[i].removeAttribute('style')
				}
				document.getElementById('box').removeAttribute('style');
			});
		},
		Project: function() {
			var live = document.getElementsByClassName('live');
			var diff = document.getElementsByClassName('diff');
			var arr = new Array();
			var sum = 0;
			for(var i = 0; i < live.length; i++) {
				live[i].innerHTML = live[i].innerText + '%' + '<span style="width:' + live[i].innerText + '%;"></span><br>';
				arr.push(diff[i].innerText);
			}
			for(var i = 0; i < arr.length; i++) {
				sum += parseFloat(arr[i]);
				if(i == 5) {
					document.getElementById('a').innerHTML = sum;
					sum = 0;
				}
				if(i == 17) {
					document.getElementById('c').innerHTML = sum;
					sum = 0;
				}
				if(i == 19) {
					document.getElementById('b').innerHTML = sum;
					sum = 0;
				}
				if(i == 20) {
					document.getElementById('d').innerHTML = sum;
					sum = 0;
				}
			}
			var td = document.getElementsByTagName("td");
			for(var i = 0; i < td.length; i++) {
				td[i].onmouseover = function() {
					this.style.backgroundColor = "#88caff";
				}
				td[i].onmouseout = function() {
					this.removeAttribute("style");
				}
			}

			document.getElementById('mask').addEventListener('click', function() {
				this.removeAttribute('class');
				var t = document.getElementById('tbody').getElementsByTagName('tr');
				for(var i = 0; i < t.length; i++) {
					t[i].removeAttribute('style')
				}
				document.getElementById('box').removeAttribute('style');
				document.getElementsByName('rate')[0].removeAttribute('rate');
			});
		},
		check: function() {
			var z = document.getElementsByName('acount')[0].value;
			var p = document.getElementsByName('password')[0].value;
			if(z == undefined || p == undefined || z == '' | p == '') {
				alert(config[1]);
				return false;
			}
			if(this.RegExp(z) && this.RegExp(p)) {
				return true;
			} else {
				alert(config[1]);
			}
			return false;
		},
		Post:function(obj,id){
			var obj=obj.parentNode.parentNode;var rate=obj.children[6].children[0].getAttribute('rate');
				document.getElementsByName('rate')[0].setAttribute('rate',rate);
				obj.setAttribute('style','background:tomato');
				if(document.getElementsByName('step').length>0){document.getElementsByName('step')[0].remove();}
				var inputc=document.createElement("input");inputc.type='hidden';inputc.value=id;inputc.name='step';
				document.getElementsByClassName('form')[0].appendChild(inputc);
				document.getElementsByName('worker')[0].value=obj.children[2].innerHTML;
				document.getElementsByName('stime')[0].value=obj.children[3].innerHTML;
				document.getElementsByName('etime')[0].value=obj.children[4].innerHTML;
				document.getElementsByName('later')[0].value=obj.children[9].innerHTML;
				document.getElementsByName('tips')[0].value=obj.children[10].innerHTML;
				document.getElementsByName('rate')[0].value=rate;
				document.getElementsByClassName('sy-title')[0].innerHTML='</span><span rt style="width:'+rate+'%;"></span>'+obj.children[1].innerHTML+'（'+rate+'%）';
				document.getElementById('mask').setAttribute('class', 'mask');
				document.getElementById('box').setAttribute('style', 'position: absolute;  top: 100px; display: block;');
				document.getElementsByName('worker')[0].focus();
		},
		Pcheck:function(){
			var p=document.getElementsByName('worker')[0].value;
				var s=document.getElementsByName('stime')[0].value;
				var e=document.getElementsByName('etime')[0].value;
				var r=parseInt(document.getElementsByName('rate')[0].value);
				var t=parseInt(document.getElementsByName('rate')[0].getAttribute('rate'));
				if(t==100){alert(config[10]);return false;}
				if(p.length<=0){alert(config[11]);return false;}
				if(s.length<=0){alert(config[12]);return false;}
				if(e.length<=0){alert(config[13]);return false;}
				if(r<=0){alert(config[14]);return false;}
				if(r<=t){alert(config[15]+config[16]+t+'%');return false;}
				return true;
		},
		cave:function(time,points){			
			document.getElementById('mask').setAttribute('class', 'mask');
			document.getElementById('box').setAttribute('style', 'position: absolute;  top: 100px; display: block;');
			
			var myChart = echarts.init(document.getElementById('main'));
			var option = {title:{text:'进度申报统计'},xAxis: {type: 'category',boundaryGap: false,data:time.split(',')},yAxis: {type: 'value'},series: [{data: points.split(','),type: 'line',areaStyle: {}}]};
			myChart.setOption(option);
		},		
		pack: function() {
			if(typeof arguments[0] === 'object') {
				var pack = '';
				for(var i in arguments[0]) {
					pack += encodeURIComponent(i) + '=' + encodeURIComponent(arguments[0][i]) + '&';
				}
				return pack.substr(0, pack.length - 1);
			} else {
				return data;
			}
		},
		Ajax: function() {
			var method = arguments[0].type || 'GET';
			var url = arguments[0].url || null;
			var async = arguments[0].async || true;
			var datatype = arguments[0].datatype || 'json';
			var data = arguments[0].data || null;
			var timeout = parseInt(arguments[0].timeout) || 1000;
			var before = arguments[0].before || function() {};
			var success = arguments[0].success || function() {};
			var error = arguments[0].error || function() {};
			var complete = arguments[0].complete || function() {};
			method=method.toUpperCase();
			before();
			var xml = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject();
			method == 'GET' ? (url = url + '?' + this.pack(data)) : null;
			xml.open(method, url, async);
			method == 'POST' ? (xml.setRequestHeader("content-type", "application/x-www-form-urlencoded")) : null;
			xml.send(this.pack(data));
			var tflag = setTimeout(function() {
				if(xml.readyState != 4) {
					error(xml, 'timeout');
					xml.onreadystatechange = function() {};
					xml.abort();
				}
			}, timeout);
			xml.onreadystatechange = function() {
				if(xml.readyState === 4) {
					window.clearTimeout(tflag);
					var status = xml.status;
					if(status >= 200 && status < 300 || status == 304) {
						success(JSON.parse(xml.responseText));
					} else {
						error(xml, status);
					}
				}
			}
		},
		Pagination:function(obj){
			if(obj.step==undefined||obj.step.constructor!=Array||obj.step.length<3){return;}
			var middle='';var that=this;
			document.getElementById(obj.id).innerHTML='<a>&#9668;</a><span></span><a>&#9658;</a>';
			if(obj.step[2]<obj.step[1]*2+7){
				for(var i=1;i<obj.step[2]+1;i++){
					if(obj.step[0]==i){middle+='<a chk>'+i+'</a>';}else{middle+='<a>'+i+'</a>';}
				}
			}else if(obj.step[0]<obj.step[1]*2+1){
				for(var i=1;i<obj.step[1]*2+4;i++){
					if(obj.step[0]==i){middle+='<a chk>'+i+'</a>';}else{middle+='<a>'+i+'</a>';}
				}
				middle+='<i>...</i><a>'+obj.step[2]+'</a>';
			}else if(obj.step[0]>obj.step[2]-obj.step[1]*2){
				middle='<a>1</a><i>...</i>';
				for(var i=obj.step[2]-obj.step[1]*2-2;i<obj.step[2]+1;i++){
					if(obj.step[0]==i){middle+='<a chk>'+i+'</a>';}else{middle+='<a>'+i+'</a>';}
				}
			}else{
				middle='<a>1</a><i>...</i>';
				for(var i=obj.step[0]-obj.step[1];i<obj.step[0]+obj.step[1]+1;i++){
					if(obj.step[0]==i){middle+='<a chk>'+i+'</a>';}else{middle+='<a>'+i+'</a>';}
				}
				middle+='<i>...</i><a>'+obj.step[2]+'</a>';
			}
			function getdata(n){
				var uid=(obj.uid==undefined?0:obj.uid);
				that.Ajax({
				    type:"POST",
				    url:obj.url,
				    data:{id:obj.pageid,step:n,uid:uid},
				    success:function(e){
				    	if(e.statu){
				    		var html='';var color=0;
				    		for(var i in e.list){
				    			switch(e.page){
				    				case 'allpj':
				    				if(parseInt(e.list[i].diff)>=1){color='style="background:red;color:#fff;"';}else{color=0;}
				    					html+='<tr '+color+'><td>'+e.list[i].id+'</td><td>'+e.list[i].pid+'</td><td>'+e.list[i].custom+'</td><td>'+e.list[i].name+'</td><td>'+e.list[i].type+'</td><td>'+e.list[i].worker+'</td><td>'+e.list[i].stime+'</td><td>'+e.list[i].etime+'</td><td>'+e.list[i].rtime+'</td><td>'+e.list[i].day+'</td><td>'+e.list[i].diff+'</td><td>'+e.list[i].rate+'</td><td>编辑</td><td>修改</td><td>图表</td></tr>';
				    				break;
				    				case 'engineer':
				    					html+='<tr><td>'+(e.list[i].id-1)+'</td><td>'+e.list[i].uid+'</td><td>'+e.list[i].name+'</td><td>'+e.list[i].depa+'</td><td>'+e.list[i].job+'</td><td>'+e.list[i].rnum+'</td><td>'+e.list[i].num+'</td><td>查看</td><td>图表</td></tr>';
				    				break;
				    				case 'usergl':
				    					html+='<tr><td>'+e.list[i].id+'</td><td>'+e.list[i].uid+'</td><td>'+e.list[i].name+'</td><td>'+e.list[i].sex+'</td><td>'+e.list[i].depa+'</td><td>'+e.list[i].job+'</td><td>'+e.list[i].time+'</td><td>'+e.list[i].phone+'</td><td>'+e.list[i].email+'</td><td>'+e.list[i].statu+'</td><td>编辑</td><td>重置</td></tr>';
				    				break;
				    				case 'worker':
				    				if(parseInt(e.list[i].time)>=1){color='style="background:red;color:#fff;"';}else{color=0;}
				    					html+='<tr '+color+'><td>'+e.list[i].id+'</td><td>'+e.list[i].pid+'</td><td>'+e.list[i].type+'</td><td>'+e.list[i].custom+'</td><td>'+e.list[i].name+'</td><td>'+e.list[i].stime+'</td><td>'+e.list[i].etime+'</td><td>'+e.list[i].day+'</td><td>'+e.list[i].time+'</td><td><li class="live">'+e.list[i].rate+'%<span style="width:'+e.list[i].rate+'%;"></span></li></td><td>编辑</td></tr>';
				    				break;
				    				case 'project':
				    					html+='<tr> <td>'+e.list[i].id+'</td> <td>'+e.list[i].pid+'</td> <td>'+e.list[i].custom+'</td> <td>'+e.list[i].name+'</td> <td>'+e.list[i].type+'</td> <td>'+e.list[i].worker+'</td> <td>'+e.list[i].stime+'</td> <td>'+e.list[i].etime+'</td> <td>'+e.list[i].rate+'</td> <td>编辑</td> </tr>';
				    				break;
				    			}
				    		}
				    		document.getElementsByTagName('tbody')[0].innerHTML=html;
				    		FONIA.Cookie({type:'set',name:e.page,data:n});
				    		FONIA.Pagination({id:obj.id,step:[n,obj.step[1],obj.step[2]],url:obj.url,pageid:obj.pageid,listen:obj.listen,uid:uid});
				    		obj.listen();
				    	}else{
				    		alert(e.msg);
				    	}			    	
				    },
				    error:function(e,t){
					    alert(t);
				    }
			   });
			}
			document.getElementById(obj.id).getElementsByTagName('span')[0].innerHTML=middle;
			document.getElementById(obj.id).innerHTML=document.getElementById(obj.id).innerHTML+'<input type="number" value=""><input type="button" value="跳转">';
			document.querySelectorAll('input[type=button]')[0].addEventListener('click',function(){
				var n=this.previousElementSibling.value==''?1:parseInt(this.previousElementSibling.value);
				if(n<1){n=1;}
				if(n>obj.step[2]){n=obj.step[2];}
				getdata(n);
			});
			document.getElementById(obj.id).querySelectorAll('span>a').forEach(function(a){
				a.addEventListener('click',function(){
					var index=parseInt(this.innerText);
					getdata(index);
				});
			});
			document.querySelectorAll('#'+obj.id+'>a').forEach(function(a,b){
				a.addEventListener('click',function(){
					var index=parseInt(document.querySelectorAll("#"+obj.id+">span>a[chk]")[0].innerText);
					if(b){			
						if(index+1>obj.step[2]){index=obj.step[2];}else{index=index+1;}
					}else{
						if(index-1<1){index=1;}else{index=index-1;}
					}
					getdata(index);					
				});
			});
		},
		Cookie:function(){
			var type = arguments[0].type || 'get';
			var name = arguments[0].name || 'FONIA';
			var data = arguments[0].data || '';
			var time = arguments[0].time || 3600;
			switch(type){
				case 'set':
					document.cookie=name+'='+JSON.stringify(data)+';expires='+(new Date(((new Date()).getTime()+time)));					
				break;
				case 'get':
					var str=document.cookie.split('; ');
					var list=Array.from(str,x=>{return x.split('=');});
					var res=null;
					for(var i in list){
						if(list[i][0]==name){res=list[i][1];break;}
					}
					return JSON.parse(res);
				break;
			}
		}
	}
})();