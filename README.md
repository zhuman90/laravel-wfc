# laravel-wfc
微信小程序模板消息群发，laravel实现

## 安装
```
composer require laravuel/laravel-wfc
```

## 配置
1. 在 config/app.php 注册 ServiceProvider
```
'providers' => [
    // ...
    Laravuel\LaravelWFC\ServiceProvider::class,
],
```
2. 创建配置文件
```
php artisan vendor:publish --provider="Laravuel\LaravelWFC\ServiceProvider"
```
3. 修改应用根目录下的 config/wfc.php 中对应的参数即可。

## 使用
1. 收集一个formId

```
use Laravuel\LaravelWFC\Collector;

// openid可以根据自身业务来获取
$collector = new Collector($openid);
$collector->save($request->formId);
```
2. 给目标用户发送模板消息

```
use Laravuel\LaravelWFC\Collector;

$collector = new Collector($openid);
$collector->send($openid, [
    'template_id' => 'template-id',
    'page' => 'index',
    'data' => [
        'keyword1' => 'VALUE',
        'keyword2' => 'VALUE2',
        // ...
    ],
]);
```
