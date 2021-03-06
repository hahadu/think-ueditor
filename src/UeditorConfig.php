<?php
/**
 *  +----------------------------------------------------------------------
 *  | Created by  hahadu (a low phper and coolephp)
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2020. [hahadu] All rights reserved.
 *  +----------------------------------------------------------------------
 *  | SiteUrl: https://github.com/hahadu/wechat
 *  +----------------------------------------------------------------------
 *  | Author: hahadu <582167246@qq.com>
 *  +----------------------------------------------------------------------
 *  | Date: 2020/11/6 下午6:52
 *  +----------------------------------------------------------------------
 *  | Description:   Config
 *  +----------------------------------------------------------------------
 **/

namespace Hahadu\ThinkUeditor;


class UeditorConfig
{
    public function get_config($filed){
        return self::$$filed;
    }

    private static $disks = "public";
    private static $images = [ //图片上传
            "add_water"=>true, //是否添加水印
            "path" =>  "images",  //保存路径
            "maxSize" => 2048000, //文件最大尺寸
            'fieldName' => 'upfile', // 提交的图片表单名称
            "allowFiles" => [ //设置允许的文件格式
                "png", "jpg", "jpeg", "gif", "bmp"
            ],

    ];
    private static $scrawl = [ //涂鸦图片
        "add_water"=>true, //是否添加水印
        "oriName" => "_temp_scrawl.png", //缓存文件名
        "path" =>  "images",  //保存路径
        "maxSize" => 2048000, //文件最大尺寸
        "fieldName"=> "upfile", /* 提交的图片表单名称 */
        "allowFiles" => [ //设置允许的文件格式
            "png",
        ],

    ];
    private static $videos = [
        "add_water"=>false, //是否添加水印
        "path" => 'videos',
        "maxSize" => 102400000,
        "fieldName"=> "upfile", /* 提交的图片表单名称 */
        "allowFiles" => [
            "flv", "swf", "mkv", "avi", "rm", "rmvb", "mpeg", "mpg",
            "ogg", "ogv", "mov", "wmv", "mp4", "webm", "mp3", "wav", "mid"
        ],
    ];
    private static $files = [ //附件上传
        "add_water"=>true, //是否添加水印 仅图片格式有效
        "path" => "files",
        "maxSize" => 51200000,
        "fieldName"=> "upfile", /* 提交的图片表单名称 */
        "allowFiles" => [
            "png", "jpg", "jpeg", "gif", "bmp","exe",
            "flv", "swf", "mkv", "avi", "rm", "rmvb", "mpeg", "mpg",
            "ogg", "ogv", "mov", "wmv", "mp4", "webm", "mp3", "wav", "mid",
            "rar", "zip", "tar", "gz", "7z", "bz2", "cab", "iso",
            "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf", "txt", "md", "xml"
        ]

    ];
    private static $catcher= [ //抓取远程图片
        "add_water"=>true, //是否添加水印
        "path" => "images",
        "maxSize" => 2048000,
        "fieldName"=>'source',
        "allowFiles" =>  [ //设置支持的文件格式
            "png", "jpg", "jpeg", "gif", "bmp"
        ],
       "oriName" =>  "_temp_catcher.png",
    ];

    private static $snapscreen=[
        /* 截图工具上传 */
        "add_water"=>false, //是否添加水印
        "path"=> "images",
    ];
    private static $manager=[
        'file'=>[
            /* 列出指定目录下的文件 */
            "listPath"=> "/upload/files/", /* 指定要列出文件的目录 */
            "listSize"=>  20, /* 每次列出文件数量 */
            "allowFiles"=>  [
                ".png", ".jpg", ".jpeg", ".gif", ".bmp",
                ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
                ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
                ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
                ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
            ] /* 列出的文件类型 */
        ],
        'image'=>[
            /* 列出指定目录下的图片 */
            "listPath"=> "/upload/images/", /* 指定要列出图片的目录 */
            "listSize"=>  20, /* 每次列出文件数量 */
            "allowFiles"=>  [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 列出的文件类型 */

        ],
    ];

    static public function get_disks(){
        if(!empty(config('ueditor.disks'))){
            return config('ueditor.disks');
        }
        return self::$disks;
    }

    static public function get_manager($filed=''){
        $result = array_replace_recursive(self::$manager,config('ueditor.manager'));
        if(null!=$filed){
            $result = $result[$filed];
        }
        $result['disks'] = self::get_disks();
        return $result;
    }
    static public function get_up_snapscreen(){
        $result = array_replace_recursive(self::$snapscreen,config('ueditor.snapscreen'));
        $result['disks'] = self::get_disks();
        return $result;

    }
    static public function get_up_catcher(){
        $result = array_replace_recursive(self::$catcher,config('ueditor.catcher'));
        $result['disks'] = self::get_disks();
        return $result;

    }
    static public function get_up_files(){
        $result = array_replace_recursive(self::$files,config('ueditor.files'));
        $result['disks'] = self::get_disks();
        return $result;

    }
    static public function get_up_videos(){

        $result = array_replace_recursive(self::$videos,config('ueditor.videos'));
        $result['disks'] = self::get_disks();
        return $result;

    }
    static public function get_up_image(){
        $result = array_replace_recursive(self::$images,config('ueditor.images'));
        $result['disks'] = self::get_disks();
        return $result;

    }
    static public function get_up_scrawl(){

        $result = array_replace_recursive(self::$scrawl,config('ueditor.scrawl'));
        $result['disks'] = self::get_disks();
        return $result;

    }

}