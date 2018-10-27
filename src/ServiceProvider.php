<?php

/*
 * This file is part of the laravuel/laravel-wfc.
 *
 * (c) laravuel <45761113@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Laravuel\LaravelWFC;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $source = __DIR__.'/config.php';
        $this->publishes([
            $source => config_path('wfc.php')
        ]);
        $this->mergeConfigFrom($source, 'wfc');
    }
}
