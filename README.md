# blog-php
www.2fuu.com


#nginx配置

```
server {
    listen      80;
    server_name blog.2fuu.dev;
    root        /var/www/html/blog.2fuu.com/www/;
    index       index.html index.htm;
    charset     utf-8;


    location /api {
            try_files $uri $uri/ /index.php?_url=$uri&$args;
    }

    location ~ \.php {
            include fastcgi_params;

            set $real_script_name $fastcgi_script_name;
            set $path_info "";
            set $real_script_name $fastcgi_script_name;
            #if ($fastcgi_script_name ~ "^(.+\.php)(/.+)$") {
            #       set $real_script_name $1;
            #       set $path_info $2;
            #}
            fastcgi_split_path_info       ^(.+\.php)(/.+)$;
            fastcgi_param PATH_INFO       $fastcgi_path_info;
            fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
    }

    location ~* ^/(frontend|css|img|js|flv|swf|download)/(.+)$ {

    }

    location ~ /\.ht {
            deny all;
    }
}

````

