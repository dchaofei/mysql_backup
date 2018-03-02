<?php
/**
 * Date: 18-3-2
 * Time: 下午4:13
 */

class Backup
{
    private $config;
    private $tmpDir = '/tmp/';
    private $dir = '/tmp/';
    private $tmpBak;
    private $bak;

    public function __construct()
    {
        $this->config = require 'config/config.php';
        $this->tmpBak = $this->tmpDir . $this->config['database_name'] . '_tmp.sql';
        $this->bak = $this->dir . $this->config['database_name'] . '_bak.sql';

        $this->checkDir();
    }

    public function backup()
    {
        $sql = sprintf('mysqldump -u%1$s -p%2$s %3$s > ' . $this->tmpBak,
            $this->config['user'],
            $this->config['password'],
            $this->config['database_name']
        );


        //var_dump($this->compare($this->config['database_name']));exit;
        if (!$this->command($sql) && $this->compare()) {
            //$this->command("rm $this->tmpBak");
        } else {
            $this->command("cp $this->tmpBak $this->bak");
            $this->sendMail();
            //echo "移动成功";
        }
    }

    private function command($str)
    {
        exec($str, $output, $return);
        return $return;
    }

    private function compare()
    {
        return $this->md5File($this->tmpBak) == $this->md5File($this->bak);
    }

    private function md5File($filename)
    {
        if (!file_exists($filename)) {
            return '';
        } else {
            $contents = file_get_contents($filename);
            return md5(substr($contents, 0, -20));
        }
    }

    private function checkDir()
    {
        if (!file_exists($this->tmpDir)) {
            mkdir($this->tmpDir, 777);
        }

        if (!file_exists($this->dir)) {
            mkdir($this->dir, 777);
        }
    }

    public function sendMail()
    {
        // 有些服务器不支持 25 端口，所以要用 ssl 方式
        $transport = (new Swift_SmtpTransport($this->config['email_service']['domain'], 465, 'ssl'))
            ->setUsername($this->config['email_service']['user'])
            ->setPassword($this->config['email_service']['password']);

        $mailer = new Swift_Mailer($transport);

        $message = (new Swift_Message('博客数据库备份'))
            ->setFrom(['dchaofei@163.com' => '飞哥'])
            ->setTo($this->config['to_email'])
            ->setBody("博客数据库备份", 'text/html', 'utf-8')
            ->attach(Swift_Attachment::fromPath($this->bak));

        // Send the message
        $result = $mailer->send($message);

        var_dump($result);
    }
}