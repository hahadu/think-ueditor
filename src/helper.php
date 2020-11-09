<?php
/**
 *  +----------------------------------------------------------------------
 *  | Created by  hahadu (a low phper and coolephp)
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2020. [hahadu] All rights reserved.
 *  +----------------------------------------------------------------------
 *  | SiteUrl: https://github.com/hahadu/think-ueditor
 *  +----------------------------------------------------------------------
 *  | Author: hahadu <582167246@qq.com>
 *  +----------------------------------------------------------------------
 *  | Date: 2020/11/9 上午9:39
 *  +----------------------------------------------------------------------
 *  | Description:   helper
 *  +----------------------------------------------------------------------
 **/
use Hahadu\ThinkUeditor\ThinkUeditor;
if(!function_exists('ueditor')){
    /*****
     * 返回ueditor数据
     * @param false $callback_array 是否返回数组 true 返回数组，false 返回json
     * @return mixed|string|\think\response\Json
     */
    function ueditor($callback_array=false){
        $ueditor = new ThinkUeditor();
        $editor = $ueditor->ueditor();
        if(true == $callback_array){
            return json_decode($editor);
        }
        return $editor;
    }
}

