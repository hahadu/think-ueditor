<?php

/**
 * Created by JetBrains PhpStorm.
 * User: taoqili
 * Date: 12-7-18
 * Time: 上午11: 32
 * UEditor编辑器通用上传类
 */
namespace Hahadu\ThinkUeditor;
use think\Exception;
use think\facade\Filesystem;
use think\File;
use think\file\UploadedFile;

class ThinkUploader
{
    private $fileField; //文件域名
    /****
     * @var UploadedFile;
     */
    private $file; //文件上传对象
    private $saveName;
    private $disk = 'public';
    private $type;
    private $base64; //文件上传对象
    private $config; //配置信息
    private $oriName; //原始文件名
    private $fileName; //新文件名
    private $fullName; //完整文件名,即从当前配置目录开始的URL
    private $filePath; //完整文件名,即从当前配置目录开始的URL
    private $fileSize; //文件大小
    private $fileType; //文件类型
    private $stateInfo; //上传状态信息,
    private $stateMap = array( //上传状态映射表，国际化用户需考虑此处数据的国际化
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
    public function __construct($fileField, $config, $type = "upload")
    {
        $this->fileField = $fileField;
        $this->disk = 'public';
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

    /**
     * 上传文件的主处理方法
     * @return mixed
     */
    private function upFile()
    {
        try{
            file_put_contents('$this.txt',$this);
            $this->file = request()->file($this->fileField);

            $this->saveName = Filesystem::disk($this->disk)->putFile( 'images', $this->file);
            file_put_contents('savename.txt',$this->saveName);
            $this->oriName = $this->saveName; //文件名
            $this->fileSize = $this->file->getSize(); //文件大小
            $this->fileType = $this->getFileExt(); //end
            $this->fullName = $this->getFullName(); //获取web可访问的文件路径
            $this->filePath = $this->getFilePath(); //end
            $this->fileName = $this->getFileName();
            $this->stateInfo = $this->stateMap[0];
        }catch (Exception $e){
            $this->stateInfo = $e->getMessage();
        }
        file_put_contents('file.txt',$this->stateInfo);

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
     * 处理base64编码的图片上传
     * @return mixed
     */
    private function upBase64()
    {
        try{
            $this->base64_cache_files();
            $this->file = request()->file($this->fileField);
            $this->saveName = Filesystem::disk($this->disk)->putFile( 'images', $this->file);;
            $this->oriName = $this->saveName; //文件名
            $this->fileSize = $this->file->getSize(); //文件大小
            $this->fileType = $this->getFileExt(); //end
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

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);

        $this->oriName = $m ? $m[1]:"";
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->stateInfo = $this->getStateInfo("ERROR_CREATE_DIR");
            return;
        } else if (!is_writeable($dirname)) {
            $this->stateInfo = $this->getStateInfo("ERROR_DIR_NOT_WRITEABLE");
            return;
        }

        //移动文件
        if (!(file_put_contents($this->filePath, $img) && file_exists($this->filePath))) { //移动失败
            $this->stateInfo = $this->getStateInfo("ERROR_WRITE_CONTENT");
        } else { //移动成功
            $this->stateInfo = $this->stateMap[0];
        }

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

    private function validate(){
        $validate = [

        ];
        return validate($validate);
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    private function getFileExt()
    {
        return $this->file->getOriginalExtension();
    }

    private function base64_cache_files(){
        $base64Data = request()->post($this->fileField);
        $img = base64_decode($base64Data);

        $cache_path = Filesystem::path($this->config['oriName']);
        file_put_contents($cache_path,$img);
        $this->file = new File($cache_path);
        return $this->file;
    }


    /**
     * 重命名文件
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
        file_put_contents('ext.txt',$this->getFileExt());
        return in_array($this->getFileExt(), $this->config["allowFiles"]);
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
     * 获取当前上传成功文件的各项信息
     * @return array
     */
    public function getFileInfo()
    {
        //添加水印
        try {
            if(config('water.add_water_type')!=0){
                if(!empty($this->fullName)){
                    $this->fullName = add_water('.'.$this->fullName);
                }
            }
        }catch (\Exception $e){
            $this->stateInfo = $e->getMessage();
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

}
