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
 *  | Date: 2020/11/8 下午5:09
 *  +----------------------------------------------------------------------
 *  | Description:   WechatSDK
 *  +----------------------------------------------------------------------
 **/

namespace Hahadu\ThinkUeditor\Uploader;


use Hahadu\Helper\FilesHelper;
use think\facade\Filesystem;
use think\File;

class Uploader extends BaseUploader
{
    public function __construct($fileField, $config, $type = "upload")
    {

        parent::__construct($fileField, $config, $type);
    }

    /**
     * 获取当前上传成功文件的各项信息
     * @return array
     */
    public function getFileInfo()
    {
        //添加水印
        $type = FilesHelper::get_file_type($this->fullName);
        if($this->config['add_water']==true && $type=='image'){
            $this->add_water();
        }

        return array(
            "state" => $this->stateInfo,
            "url" => $this->fullName,
            "title" => $this->fileName,
            "original" => $this->oriName,
            "type" => '.'.$this->fileType,
            "size" => $this->fileSize
        );
    }

    /**
     * 上传文件的主处理方法
     * @return mixed
     */
    protected function upFile()
    {
        $this->file = request()->file($this->fileField);
        $this->uploadFile();

    }
    /**
     * 处理base64编码的图片上传
     * @return mixed
     */
    protected function upBase64()
    {
        $base64Data = request()->post($this->fileField);
        $file_info = base64_file_info($base64Data,$this->config['oriName']);
        $this->config['oriName'] = $file_info->getFilename();
        $this->file = $file_info;
        $this->uploadFile();
    }


}