<?php


$router->group(['prefix' => ''], function () use ($router)
{
    /**
     * 接口
     */
    $router->group(['prefix' => 'api'], function () use ($router)
    {
        //前度接口
        $router->group(['namespace' => 'Api'], function() use ($router)
        {
            //基础
            $router->group(['namespace' => 'Base'], function() use ($router) {
                //上传图片
                $router->post('upload/images', ['uses' => 'UploadController@images']);
            });

            //分类
            $router->group(['namespace' => 'Classify'], function() use ($router) {
                //列表
                $router->get('classify/list',  ['uses' => 'IndexController@index']);

            });

            //文章
            $router->group(['namespace' => 'Article'], function() use ($router) {
                //列表
                $router->get('article/list',  ['uses' => 'IndexController@index']);
                //详情
                $router->get('article/info',  ['uses' => 'IndexController@info']);
            });
        });
    });


    /**
     * 后台接口
     */
    $router->group(['prefix' => 'back'], function () use ($router)
    {
        $router->group(['namespace' => 'Back'], function() use ($router)
        {
            //登录
            $router->group(['namespace' => 'Login'], function() use ($router) {
                //登录
                $router->post('user/login', ['uses' => 'IndexController@login']);
                //退出
                $router->post('user/logout', ['uses' => 'IndexController@logout']);
            });

            //股票
            $router->group(['namespace' => 'Stock'], function() use ($router) {

                $router->post('stock/index', ['uses' => 'IndexController@index']);
            });


        });

    });
});




