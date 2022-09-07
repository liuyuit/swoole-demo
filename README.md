## swoole

#### 系统要求

- linux
- php  7.4.19
- swoole   5.0.0-dev
- supervisor
- composer

#### 项目构建

```
composer install
```

```
cp .env.example .env
vim .env
```

```
php websocket/server.php
```

测试页面地址 `http://swoole-demo.local/websocket/client/private.html`
#### 常见问题
``` Uncaught Error: Class 'Co' not found```
需要开启 [协程短名称](https://wiki.swoole.com/#/other/alias?id=%E5%8D%8F%E7%A8%8B%E7%9F%AD%E5%90%8D%E7%A7%B0)
