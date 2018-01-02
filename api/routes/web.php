<?php


$router->group(['prefix' => 'api', 'namespace' => 'Api'], function () use ($router)
{
    /**
     * 前端接口
     */
    $router->group(['prefix' => 'front', 'namespace' => 'Front'], function () use ($router)
    {
        //首页
        $router->group(['namespace' => 'Home'], function() use ($router) {
            //首页
            $router->get('home/list',  ['uses' => 'IndexController@index']);

        });

        //文章
        $router->group(['namespace' => 'Article'], function() use ($router) {
            //列表
            $router->get('article/list',  ['uses' => 'IndexController@index']);
            //详情
            $router->get('article/info',  ['uses' => 'IndexController@info']);
        });
    });


    /**
     * 后端接口
     */
    $router->group(['prefix' => 'back','namespace' => 'Back'], function () use ($router)
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

    /**
     * 基础接口
     */
    $router->group(['prefix' => 'base', 'namespace' => 'Base'], function () use ($router)
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

    });

});
