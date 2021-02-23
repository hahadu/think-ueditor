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
 *  | Date: 2020/11/6 下午6:44
 *  +----------------------------------------------------------------------
 *  | Description:   Ueditor
 *  +----------------------------------------------------------------------
 **/

namespace Hahadu\ThinkUeditor;
use Hahadu\ThinkUeditor\Uploader\Uploader;
use Hahadu\Helper\JsonHelper;
use Hahadu\ThinkUeditor\UeditorConfig;
use think\Exception;

class ThinkUeditor
{
    public $config ;
    public function __construct(){
        $this->config = new UeditorConfig();
    }

    /******
     * @return string|\think\response\Json
     */
    public function ueditor(){

        $action = request()->param('action');

        switch ($action) {
            case 'config':
                $result = $this->conf_die();
                break;

            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':

                /* 上传文件 */
            case 'uploadfile':
                $result = $this->upload($action);

                break;

            /* 列出文件 */
            case 'listfile':
                /* 列出图片 */
            case 'listimage':
                $result = $this->list($action);
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $result = $this->crawler();
                break;

            default:
                $result = array(
                    'state'=> '请求地址出错'
                );
                break;
        }

        /* 输出结果 */

        if (!empty(request()->param("callback"))) {
            if (preg_match("/^[\w_]+$/", request()->param("callback"))) {
                if (class_exists('\think\response\\' . ucfirst(strtolower(request()->param("callback"))))) {
                    return response($result, 200, [], request()->param("callback"))->options([]);
                } else {
                    return htmlspecialchars(request()->param("callback")) . '(' . JsonHelper::json_encode($result) . ')';
                }
            } else {
                return json(make_array(array(
                    'state' => 'callback参数不合法'
                )));
            }
        } else {
            return json(make_array($result));
        }
    }

    /****
     * 上传文件
     * @param $_conf
     * @param $action
     * @return false|string|array
     */
    private function upload($action){

        /* 上传配置 */
        $base64 = "upload";
        switch ($action) {
            case 'uploadimage':
                $config = $this->config::get_up_image();
                $fieldName = $config['fieldName'];
                break;
            case 'uploadscrawl':

                $config = $this->config::get_up_scrawl();
                $fieldName = $config['fieldName'];
                $base64 = "base64";
                break;
            case 'uploadvideo':
                $config = $this->config::get_up_videos();
                $fieldName = $config['fieldName'];
                break;
            case 'uploadfile':
            default:
                $config = $this->config::get_up_files();
                $fieldName = $config['fieldName'];
                break;
        }
        /* 生成上传实例对象并完成上传 */
        $up = new Uploader($fieldName, $config, $base64);
        $result = $up->getFileInfo();

        /**
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
         *     "url" => "",            //返回的地址
         *     "title" => "",          //新文件名
         *     "original" => "",       //原始文件名
         *     "type" => ""            //文件类型
         *     "size" => "",           //文件大小
         * )
         */

        /* 返回数据 */
        return $result;
    }

    private function list($action){
        $manager = $this->config::get_manager();
        switch ($action) {
            /* 列出文件 */
            case 'listfile':
                $allowFiles = $manager['file']['allowFiles'];
                $listSize = $manager['file']['listSize'];
                $path = $manager['file']['listPath'];
                break;
            /* 列出图片 */
            case 'listimage':
            default:
                $allowFiles = $manager['image']['allowFiles'];
                $listSize = $manager['image']['listSize'];
                $path = $manager['image']['listPath'];
        }
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

        /* 获取参数 */
        $size=request()->param('size');
        $start=request()->param('start');
        $size = isset($size) ? htmlspecialchars($size) : $listSize;
        $start = isset($start) ? htmlspecialchars($start) : 0;
        $end = $start + $size;

        /* 获取文件列表 */
        $path = request()->server('DOCUMENT_ROOT') . (substr($path, 0, 1) == "/" ? "":"/") . $path;
        $files = $this->getfiles($path, $allowFiles);
        if (!count($files)) {
            return array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files)
            );
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }
//倒序
//for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
//    $list[] = $files[$i];
//}

        /* 返回数据 */
        $result = array(
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        );

        return $result;

    }

    private function crawler(){
        /* 上传配置 */
        $config = $this->config::get_up_catcher();
        $fieldName = $config['fieldName'];

        /* 抓取远程图片 */
        $list = array();
        if (!empty(request()->post($fieldName))) {
            $source = request()->post($fieldName);
        } else {
            $source = request()->param($fieldName);
        }
        foreach ($source as $imgUrl) {
            $item = new Uploader($imgUrl, $config, "remote");
            $info = $item->getFileInfo();
            array_push($list, array(
                "state" => $info["state"],
                "url" => $info["url"],
                "size" => $info["size"],
                "title" => htmlspecialchars($info["title"]),
                "original" => htmlspecialchars($info["original"]),
                "source" => htmlspecialchars($imgUrl)
            ));
        }

        /* 返回抓取数据 */
        return array(
            'state'=> count($list) ? 'SUCCESS':'ERROR',
            'list'=> $list
        );
    }


    public function getfiles($path, $allowFiles, &$files = array())
    {
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                        $files[] = array(
                            'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }

    /****
     * 编辑器需要一个原生的配置项来完成验证，所以这个文件是假的，不要动
     * @return false|string
     */
    private function conf_die(){
        if(is_file(config_path('config.json'))){
            $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(config_path('config.json'))), true);
        }else{
            $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(dirname(__FILE__)."/config.json")), true);
        }


        return $CONFIG;
    }

}