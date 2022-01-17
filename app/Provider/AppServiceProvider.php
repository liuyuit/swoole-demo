<?php

namespace App\Provider;

class AppServiceProvider
{
    protected $appBasePath;

    public function boot($appBasePath)
    {
        $this->appBasePath = $appBasePath;
        $this->registerHelpers();
    }

    /**
     * 引入助手函数
     */
    protected function registerHelpers()
    {
        foreach (glob($this->appBasePath . '/app/Helpers/*.php') as $file) {
            require $file;
        }
    }
}
