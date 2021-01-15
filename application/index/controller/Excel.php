<?php

namespace app\index\controller;
class Excel extends \think\Controller
{
	function index()
	{
		return $this->fetch();
	}
	
    
    function gtexcel()
    {
    	if($_SERVER['REQUEST_METHOD']=='POST' ?? false){
    		$file = $_FILES['file']['tmp_name'];
       	 	$data = $this->import_excel($file,intval($_POST['type']));	
    	}    	
        exit('{"statu":1,"list":'.json_encode($data).'}');
    	
    }
    
    function import_excel($file,$t){
        // 判断文件是什么格式
        $type = pathinfo($file);
        $type = strtolower($type["extension"]);
        $type=$type==='csv' ? $type : 'Excel5';
        ini_set('max_execution_time', '0');
        \think\Loader::import('PHPExcel.PHPExcel');
        // 判断使用哪种格式
        $objReader = \PHPExcel_IOFactory::createReader($type);
        $objPHPExcel = $objReader->load($file);
        $sheet = $objPHPExcel->getSheet(0);
        // 取得总行数
        $highestRow = $sheet->getHighestRow();
        // 取得总列数
        $highestColumn = $sheet->getHighestColumn();
        //循环读取excel文件,读取一条,插入一条
        $data=array();
        //从第一行开始读取数据
        for($j=2;$j<=$highestRow;$j++){
            //从A列读取数据
            for($k='A';$k<=$highestColumn;$k++){
                // 读取单元格
                if($t){
                	if($k=='F'){
                		$data[$j][]=trim(\PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue()));
                	}else{
                		 $data[$j][]=trim($objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue());
                	}
                }else{
                	if($k=='H'||$k=='I'){
                		$data[$j][]=trim(\PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue()));
                	}else{
                		 $data[$j][]=trim($objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue());
                	}
                }
                //$data[$j][]=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
            }
        }
        return $data;
    }

}
?>