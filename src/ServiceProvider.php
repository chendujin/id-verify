<?php
namespace Chendujin\IdValidator;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function boot():
    {
        $validator = $this->app['validator'];
        $validator->extend('id_verify', function ($attribute, $value, $paramters, $validator) {
            return  (new IdValidator())->isValid($value);
        });
    }
}
