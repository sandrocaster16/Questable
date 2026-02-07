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
        'css/app.css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
        'css/components.css',
        'css/custom.css',
        'css/layout.css',
        'css/pages.css',
        'css/reset.css',
        'css/variables.css',
        'css/dark-theme.css',
        'css/mobile.css',
    ];
    public $js = [
        'plugins/jquery/jquery-3.7.1.min.js',
        'plugins/ckeditor/js/ckeditor.js',
        'plugins/ckeditor/js/ckeditor-translate-ru.js',
//        'metronic/js/plugins.bundle.js',
//        'metronic/js/scripts.bundle.js',
        'js/theme.js',
        'js/slider.js',
        'js/sidebar.js',
        'js/settings-modal.js',
        'js/stations.js',
        'js/qrcodes.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}
