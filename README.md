# think-ueditor
thinkphp6-ueditor文件上传模块

###安装 

####先决条件
必须安装thinkphp6

####安装
composer require hahadu/think-ueditor

### 使用：
```php
//在当前控制器或者公共控制器中
    public function ueditor(){
        $ueditor = new ThinkUeditor();
        return $ueditor->ueditor(); //如果tp开发模式编辑器提示配置错误，可以尝试下面这一行
        //echo $ueditor->ueditor();die;
    }
//或者使用便捷函数ueditor()
    public function ueditor(){
        return ueditor(); 
    }
``` 
然后模板文件：
```html
<script type="text/javascript" src="/static/plugins/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/static/plugins/ueditor/ueditor.all.js"></script>
<textarea id="container" name=content></textarea>
<script>var ue = UE.getEditor("container",{
initialFrameHeight:500,
allowDivTransToP: false,
serverUrl : "{:url('ueditor')}",//路由地址
});
</script>
<script>
</script>
```
#### 配置：

在系统config配置文件夹中配置ueditor编辑器和水印文件
>config
>>ueditor.php //编辑器配置文件
>>water.php //水印配置文件

详情参考config目录下ueditor.php和water.php


#### 鸣谢
> [hahadu/image-factory](https://github.com/hahadu/image-factory) 提供的图像处理模块
>
> [hahadu/helper-function](https://github.com/hahadu/helper-function) 一些助手函数
>
>[hahadu/think-helper](https://github.com/hahadu/think-helper) 另外一些助手函数