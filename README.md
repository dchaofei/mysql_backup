有一次服务器崩了，博客数据差点找不回来，所以写个备份程序 : )

如果指定数据库有变化就发送备份文件到指定邮箱。

### 使用
1. 配置 `config/config.php`
2. 执行 `composer install`
3. 把 `index.php` 加入定时任务