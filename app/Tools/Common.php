<?php
namespace App\Tools;
class Common{
    /**
     *  权限添加视图，封装的无限极
     */
    public static function getPower($data,$parent_id=0,$level=0){
        static $info=[];
        
        foreach ($data as $key => $value) {
            if($value['parent_id']==$parent_id){
                $value['level']=$level;
                $info[]=$value;
                self::getPower($data,$value['power_id'],$level+1);
            }
        }
        return $info;
        
    }


   	/**
	 * 递归排序 把二级分类放到 1级 son字段里
	 * @param  [type]  $data      [description]
	 * @param  integer $parent_id [description]
	 * @return [type]             [description]
	 */
	public function createTreeBySon($data,$parent_id=0)
	{	
		//定义一个容器
		$new_arr = [];
		//循环比对
		foreach ($data as $key => $value) {
			//判断 
			if($value['parent_id'] == $parent_id){
				//找到了
				$new_arr[$key] = $value;
				//找子分类 
				$new_arr[$key]['son'] = $this->createTreeBySon($data,$value['power_id']);
			}
		}
		return $new_arr;
	}

}
    
?>