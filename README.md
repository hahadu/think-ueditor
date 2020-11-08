# think-ueditor
thinkphp6-ueditor文件上传模块

安装 composer require hahadu/think-ueditor

使用：
```php
//在当前控制器或者公共控制器中
    public function ueditor(){
        $ueditor = new ThinkUeditor();
        echo $ueditor->ueditor(); 
        die; //防止track冲突，开发模式不要用return
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
配置：

在系统config配置文件夹中配置ueditor编辑器和水印文件
>config
>>ueditor.php //编辑器配置文件
>>water.php //水印配置文件

详情参考config目录下ueditor.php和water.php