<?php
return array(
	/* 项目设定 */
    
    'APP_FILE_CASE'         => false,   // 是否检查文件的大小写 对Windows平台有效
    'APP_AUTOLOAD_PATH'     => '',// 自动加载机制的自动搜索路径,注意搜索顺序
    'APP_TAGS_ON'           => true, // 系统标签扩展开关
    'APP_SUB_DOMAIN_DEPLOY' => false,   // 是否开启子域名部署
    'APP_SUB_DOMAIN_RULES'  => array(), // 子域名部署规则
    'APP_SUB_DOMAIN_DENY'   => array(), //  子域名禁用列表
    'APP_GROUP_LIST'        => '',      // 项目分组设定,多个组之间用逗号分隔,例如'Home,Admin'
    'APP_GROUP_MODE'        =>  0,  // 分组模式 0 普通分组 1 独立分组
    'APP_GROUP_PATH'        =>  'Modules', // 分组目录 独立分组模式下面有效
    'ACTION_SUFFIX'         =>  '', // 操作方法后缀

     /* 数据库设置 */
	'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_NAME'   => 'myapp', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '',
    'DB_PORT'   => '3306',
    'DB_PREFIX' => '',
 //   'DB_DSN'    => 'mysql://root:@localhost/myapp',
    'USER_AUTH_KEY'=>'authId',

    /* SESSION设置 */
    'SESSION_AUTO_START'    => false,    // 是否自动开启Session
    'SESSION_OPTIONS'       => array(), // session 配置数组 支持type name id path expire domain 等参数
    'SESSION_TYPE'          => '', // session hander类型 默认无需设置 除非扩展了session hander驱动
    'SESSION_PREFIX'        => '', // session 前缀
    'VAR_SESSION_ID'      => 'session_id',     //sessionID的提交变量

    /* 系统变量名称设置 */
    'VAR_GROUP'             => 'g',     // 默认分组获取变量
    'VAR_MODULE'            => 'm',		// 默认模块获取变量
    'VAR_ACTION'            => 'a',		// 默认操作获取变量
    'VAR_AJAX_SUBMIT'       => 'ajax',  // 默认的AJAX提交变量
	'VAR_JSONP_HANDLER'     => 'callback',
    'VAR_PATHINFO'          => 's',	// PATHINFO 兼容模式获取变量例如 ?s=/module/action/id/1 后面的参数取决于URL_PATHINFO_DEPR
    'VAR_URL_PARAMS'        => '_URL_', // PATHINFO URL参数变量
    'VAR_TEMPLATE'          => 't',		// 默认模板切换变量
    'VAR_FILTERS'           =>  'filter_exp',     // 全局系统变量的默认过滤方法 多个用逗号分割

    'OUTPUT_ENCODE'         =>  false, // 页面压缩输出
    'HTTP_CACHE_CONTROL'    =>  'private', // 网页缓存控制

    /*调试变量设置 */
    'SHOW_RUN_TIME'    => true, // 运行时间显示
 	'SHOW_ADV_TIME'    => true, // 显示详细的运行时间
 	'SHOW_DB_TIMES'    => true, // 显示数据库查询和写入次数
 	'SHOW_CACHE_TIMES' => true, // 显示缓存操作次数
 	'SHOW_USE_MEM'     => true, // 显示内存开销
 	'SHOW_LOAD_FILE'   => true, // 显示加载文件数
 	'SHOW_FUN_TIMES'   => true, // 显示函数调用次数
 	'SHOW_PAGE_TRACE'  => true, // 显示页面Trace信息

    /* 模板引擎设置 */
    'TMPL_CONTENT_TYPE'     => 'text/html', // 默认模板输出类型
    'TMPL_ACTION_ERROR'     => THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE'   => THINK_PATH.'Tpl/think_exception.tpl',// 异常页面的模板文件
    'TMPL_DETECT_THEME'     => false,       // 自动侦测模板主题
    'TMPL_TEMPLATE_SUFFIX'  => '.html',     // 默认模板文件后缀
    'TMPL_FILE_DEPR'        =>  '/', //模板文件MODULE_NAME与ACTION_NAME之间的分割符
 
    /* 数据缓存设置 */
    'DATA_CACHE_TIME'       => 0,      // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS'   => false,   // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'      => false,   // 数据缓存是否校验缓存
    'DATA_CACHE_PREFIX'     => '',     // 缓存前缀
    'DATA_CACHE_TYPE'       => 'Redis',  // 数据缓存类型,

    /*Redis设置*/  
    'REDIS_HOST'            => 'localhost', //主机  
    'REDIS_PORT'            => '6379', //端口  
//  'REDIS_DBNAME'            => 'appdb', //库名  
    'REDIS_CTYPE'           => 1, //连接类型 1:普通连接 2:长连接  
    'REDIS_TIMEOUT'         => 0, //连接超时时间(S) 0:永不超时 
);
?>