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
 *  | Date: 2020/11/8 下午5:03
 *  +----------------------------------------------------------------------
 *  | Description:   ThinkUploader
 *  +----------------------------------------------------------------------
 **/

namespace Hahadu\ThinkUeditor\Uploader;


use Hahadu\Helper\FilesHelper;
use think\Exception;
use think\facade\Filesystem;
use think\File;
use think\file\UploadedFile;

class BaseUploader
{
    protected $fileField; //文件域名
    /****
     * @var UploadedFile;
     */
    protected $file; //文件上传对象
    protected $_path;
    protected $saveName;
    protected $disk = 'public';
    protected $type;
    protected $base64; //文件上传对象
    protected $config; //配置信息
    protected $oriName; //原始文件名
    protected $fileName; //新文件名
    protected $fullName; //完整文件名,即从当前配置目录开始的URL
    protected $filePath; //完整文件名,即从当前配置目录开始的URL
    protected $fileSize; //文件大小
    protected $fileType; //文件类型
    protected $stateInfo; //上传状态信息,
    protected $stateMap = array( //上传状态映射表，国际化用户需考虑此处数据的国际化
        "SUCCESS", //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "文件大小超出 upload_max_filesize 限制",
        "文件大小超出 MAX_FILE_SIZE 限制",
        "文件未被完整上传",
        "没有文件被上传",
        "上传文件为空",
        "ERROR_TMP_FILE" => "临时文件错误",
        "ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件",
        "ERROR_SIZE_EXCEED" => "文件大小超出网站限制",
        "ERROR_TYPE_NOT_ALLOWED" => "文件类型不允许",
        "ERROR_CREATE_DIR" => "目录创建失败",
        "ERROR_DIR_NOT_WRITEABLE" => "目录没有写权限",
        "ERROR_FILE_MOVE" => "文件保存时出错",
        "ERROR_FILE_NOT_FOUND" => "找不到上传文件",
        "ERROR_WRITE_CONTENT" => "写入文件内容错误",
        "ERROR_UNKNOWN" => "未知错误",
        "ERROR_DEAD_LINK" => "链接不可用",
        "ERROR_HTTP_LINK" => "链接不是http链接",
        "ERROR_HTTP_CONTENTTYPE" => "链接contentType不正确"
    );

    /**
     * 构造函数
     * @param string $fileField 表单名称
     * @param array $config 配置项
     * @param string $type	处理文件上传的方式
     */
    public function __construct($fileField, $config, $type = "upload"){
        $this->_path = $config['path'];
        $this->fileField = $fileField;
        $this->disk = $config['disks'];
        $this->config = $config;
        $this->type = $type;
        if ($type == "remote") {
            $this->saveRemote();
        } else if($type == "base64") {
            $this->upBase64();
        } else {
            $this->upFile();
        }

        $this->stateMap['ERROR_TYPE_NOT_ALLOWED'] = mb_convert_encoding($this->stateMap['ERROR_TYPE_NOT_ALLOWED'], 'utf-8', 'auto');

    }


    /****
     * @param $file
     */
    protected function uploadFile()
    {
        try{
            $this->saveName = Filesystem::disk($this->disk)->putFile( $this->config['path'], $this->file);
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
    /**
     * 上传错误检查
     * @param $errCode
     * @return string
     */
    private function getStateInfo($errCode)
    {
        return !$this->stateMap[$errCode] ? $this->stateMap["ERROR_UNKNOWN"] : $this->stateMap[$errCode];
    }
    /**
     * 拉取远程图片
     * @return mixed
     */
    private function saveRemote()
    {
        $imgUrl = htmlspecialchars($this->fileField);
        $imgUrl = str_replace("&amp;", "&", $imgUrl);

        //http开头验证
        if (strpos($imgUrl, "http") !== 0) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_LINK");
            return;
        }
        //获取请求头并检测死链
        $heads = get_headers($imgUrl, 1);
        if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            $this->stateInfo = $this->getStateInfo("ERROR_DEAD_LINK");
            return;
        }
        //格式验证(扩展名验证和Content-Type验证)
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileType, $this->config['allowFiles']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], "image")) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_CONTENTTYPE");
            return;
        }

        $img_cache = FilesHelper::download_file($imgUrl,Filesystem::path("cache_".time().'.'.$fileType));
        $this->file = new File($img_cache);

        $this->uploadFile();


    }

    protected function add_water(){
        try {
            if(config('water.add_water_type')!=0){
                if(!empty($this->fullName)){
                    $this->fullName = add_water('.'.$this->fullName);
                }
            }
        }catch (\Exception $e){
            $this->stateInfo = $e->getMessage();
        }
    }


}