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
 *  | Date: 2020/11/7 下午7:00
 *  +----------------------------------------------------------------------
 *  | Description:   WechatSDK
 *  +----------------------------------------------------------------------
 **/

namespace Hahadu\ThinkUeditor;


use think\Exception;
use think\facade\Filesystem;

trait Upfile
{
    /****
     * @param $file
     */
    private function uploadFile()
    {
        try{

            $this->saveName = Filesystem::disk($this->disk)->putFile( 'images', $this->file);
            $this->oriName = $this->saveName; //文件名
            $this->fileSize = $this->file->getSize(); //文件大小
            $this->fileType = $this->getFileExt();
            $this->fullName = $this->getFullName(); //获取web可访问的文件路径
            $this->filePath = $this->getFilePath(); //end
            $this->fileName = $this->getFileName();
            $this->stateInfo = $this->stateMap[0];
        }catch (Exception $e){
            $this->stateInfo = $e->getMessage();
        }



        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }
        //检查是否不允许的文件格式
        if (!$this->checkType()) {

            $this->stateInfo = $this->getStateInfo("ERROR_TYPE_NOT_ALLOWED");
            return;
        }

    }
    /**
     * 获取文件扩展名
     * @return string
     */
    private function getFileExt()
    {
        file_put_contents('??','');
        return $this->file->extension();
    }
    /**
     * 完整文件名
     * @return string
     */
    private function getFullName()
    {
        $url =  Filesystem::getDiskConfig($this->disk,'url');
        return $url.'/'.$this->saveName;

    }

    /**
     * 获取文件名
     * @return string
     */
    private function getFileName () {
        return substr($this->filePath, strrpos($this->filePath, '/') + 1);
    }

    /**
     * 获取文件完整路径
     * @return string
     */
    private function getFilePath()
    {
        return Filesystem::path($this->saveName);
    }


    /**
     * 文件类型检测
     * @return bool
     */
    private function checkType()
    {
        if(!empty($this->config["allowFiles"])){
            return in_array($this->getFileExt(), $this->config["allowFiles"]);
        }else{
            return true;
        }

    }

    /**
     * 文件大小检测
     * @return bool
     */
    private function  checkSize()
    {
        return $this->fileSize <= ($this->config["maxSize"]);
    }

}