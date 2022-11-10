<?php

namespace lhs\craftcms\modules\translations\assetbundles;

use craft\web\AssetBundle;

class CpAssetBundle extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@modules/translations-module/resources/cp';

        $this->css = ['cp.css'];

        parent::init();
    }

}
