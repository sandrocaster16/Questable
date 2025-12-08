<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'metronic/css/plugins.bundle.css',
        'metronic/css/style.bundle.css',
        'plugins/ckeditor/css/styles.css',
        'css/app.css',
        'css/style.css',
    ];
    public $js = [
        'plugins/jquery/jquery-3.7.1.min.js',
        'plugins/ckeditor/js/ckeditor.js',
        'plugins/ckeditor/js/ckeditor-translate-ru.js',
//        'metronic/js/plugins.bundle.js',
//        'metronic/js/scripts.bundle.js',
        'js/main.js',
        'js/dark-mode.js',
        'js/script.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}
