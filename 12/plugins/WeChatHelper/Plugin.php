<?php
/**
 * 让你的微信公众帐号和Typecho博客联系起来
 * 
 * @package WeChatHelper
 * @author 冰剑
 * @version 2.2.1
 * @link http://www.binjoo.net
 * @dependence 14.3.14
 */
class WeChatHelper_Plugin implements Typecho_Plugin_Interface {
    public static function activate() {
        $db = Typecho_Db::get();
        if("Pdo_Mysql" === $db->getAdapterName() || "Mysql" === $db->getAdapterName()){
            /**
             * 创建关键字表
             */
            $db->query("CREATE TABLE IF NOT EXISTS " . $db->getPrefix() . 'wch_keywords' . " (
                      `kid` int(11) NOT NULL AUTO_INCREMENT,
                      `name` varchar(100) NOT NULL,
                      `rid` int(11) NOT NULL,
                      PRIMARY KEY (`kid`)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
            /**
             * 创建自定义回复表
             */
            $db->query("CREATE TABLE IF NOT EXISTS " . $db->getPrefix() . 'wch_reply' . " (
                      `rid` int(11) NOT NULL AUTO_INCREMENT,
                      `keywords` varchar(200) DEFAULT NULL,
                      `type` varchar(20) DEFAULT 'text',
                      `command` varchar(20) DEFAULT NULL,
                      `param` char(1) DEFAULT '0',
                      `content` text,
                      `status` char(1) DEFAULT '0',
                      `created` int(10) DEFAULT '0',
                      PRIMARY KEY (`rid`)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
            /**
             * 创建用户管理表
             */
            $db->query("CREATE TABLE IF NOT EXISTS " . $db->getPrefix() . 'wch_users' . " (
                      `uid` int(11) NOT NULL AUTO_INCREMENT,
                      `openid` varchar(50) DEFAULT '',
                      `nickname` varchar(100) DEFAULT '',
                      `sex` char(1) DEFAULT '',
                      `language` varchar(50) DEFAULT '',
                      `city` varchar(50) DEFAULT '',
                      `province` varchar(50) DEFAULT '',
                      `country` varchar(50) DEFAULT '',
                      `headimgurl` varchar(200) DEFAULT '',
                      `subscribe_time` int(10) DEFAULT '0',
                      `credits` int(10) NOT NULL DEFAULT '0',
                      `bind` int(3) DEFAULT '0',
                      `status` char(1) DEFAULT '1',
                      `created` int(10) DEFAULT '0',
                      `synctime` int(10) DEFAULT '0',
                      PRIMARY KEY (`uid`)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
            /**
             * 创建自定义菜单表
             */
            $db->query("CREATE TABLE IF NOT EXISTS " . $db->getPrefix() . 'wch_menus' . " (
                      `mid` int(11) NOT NULL AUTO_INCREMENT,
                      `level` varchar(10) DEFAULT 'button',
                      `name` varchar(200) DEFAULT '',
                      `type` varchar(10) DEFAULT 'view',
                      `value` varchar(200) DEFAULT '',
                      `sort` int(3) DEFAULT '0',
                      `order` int(3) DEFAULT '1',
                      `parent` int(11) DEFAULT '0',
                      `created` int(10) DEFAULT '0',
                      PRIMARY KEY (`mid`)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
        }else{
            throw new Typecho_Plugin_Exception(_t('对不起, 本插件仅支持MySQL数据库。'));
        }

        $index = Helper::addMenu('微信助手');
        Helper::addAction('WeChat', 'WeChatHelper_Action');
        Helper::addPanel($index, 'WeChatHelper/Page/Config.php', '参数设置', '参数设置', 'administrator');
        Helper::addPanel($index, 'WeChatHelper/Page/Users.php', '用户管理', '用户管理', 'administrator');
        Helper::addPanel($index, 'WeChatHelper/Page/Menus.php', '自定义菜单', '自定义菜单', 'administrator');
        Helper::addPanel($index, 'WeChatHelper/Page/CustomReply.php', '自定义回复', '自定义回复', 'administrator');
        Helper::addPanel($index, 'WeChatHelper/Page/Addons.php', '插件扩展', '插件扩展', 'administrator');
        return('微信助手已经成功激活，请进入设置Token!');
    }

    public static function deactivate() {
        $db = Typecho_Db::get();
        $options = Typecho_Widget::widget('Widget_Options');
        if (isset($options->WeChatHelper_dropTable) && $options->WeChatHelper_dropTable) {
            if("Pdo_Mysql" === $db->getAdapterName() || "Mysql" === $db->getAdapterName()){
               $db->query("drop table ".$db->getPrefix()."wch_keywords, ".$db->getPrefix()."wch_reply, ".$db->getPrefix()."wch_users, ".$db->getPrefix()."wch_menus");
               $db->query($db->sql()->delete('table.options')->where('name like ?', "WeChatHelper_%"));
            }
        }
        $index = Helper::removeMenu('微信助手');
        Helper::removePanel($index, 'WeChatHelper/Page/Config.php');
        Helper::removePanel($index, 'WeChatHelper/Page/Users.php');
        Helper::removePanel($index, 'WeChatHelper/Page/Menus.php');
        Helper::removePanel($index, 'WeChatHelper/Page/CustomReply.php');
        Helper::removePanel($index, 'WeChatHelper/Page/Addons.php');
        Helper::removeAction('WeChat');
    }

    public static function config(Typecho_Widget_Helper_Form $form) {
    }
    
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
}
