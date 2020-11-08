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
 *  | Date: 2020/11/8 下午6:46
 *  +----------------------------------------------------------------------
 *  | Description:   编辑器配置文件
 *  +----------------------------------------------------------------------
 **/

return [
    'disks' =>'public',
    'images'=>[ //图片上传
        "add_water"=>true, //是否添加水印
        "path" =>  "images",  //保存路径
        "maxSize" => 2048000, //文件最大尺寸
        'fieldName' => 'upfile', // 提交的图片表单名称
        "allowFiles" => [ //设置允许的文件格式
            "png", "jpg", "jpeg", "gif", "bmp"
        ],
    ],

    'scrawl' =>[ //涂鸦图片
        "add_water"=>true, //是否添加水印
        "oriName" => md5(time().rand(10,99)).'.png', //保存文件名
        "path" =>  "images",  //保存路径
        "maxSize" => 2048000, //文件最大尺寸
        "fieldName"=> "upfile", /* 提交的图片表单名称 */
        "allowFiles" => [ //设置允许的文件格式
            "png",
        ],
    ],
    'videos'=>[ //视频
        "add_water"=>false, //是否添加水印
        "path" => 'videos',
        "maxSize" => 102400000,
        "fieldName"=> "upfile", /* 提交的图片表单名称 */
        "allowFiles" => [
            "flv", "swf", "mkv", "avi", "rm", "rmvb", "mpeg", "mpg",
            "ogg", "ogv", "mov", "wmv", "mp4", "webm", "mp3", "wav", "mid"
        ],
    ],
    'files'=>[ //附件上传
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
    ],
    'catcher'=>[
        "add_water"=>true, //是否添加水印
        "path" => "images",
        "maxSize" => 2048000,
        "fieldName"=>'source',
        "allowFiles" =>  [ //设置支持的文件格式
            ".png", ".jpg", ".jpeg", ".gif", ".bmp"
        ],
        "oriName" =>  md5(time().rand(10,99)).'.png'

    ],
    'snapscreen'=>[
        /* 截图工具上传 */
        "add_water"=>false, //是否添加水印
        "path"=> "images",
    ],
    'manager'=>[
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
    ],
];


