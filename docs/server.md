go onto mobile internet

all devices connect to that mobile internet

run following command on server laptop
```console
php artisan serve --host 0.0.0.0

npm run build
```  

all devices can now visit that laptops IP address in the browser,  
they are now all connected to one server, DB, all is in sync

to fix camera not loading in on unsafe website:  
search: "chrome://flags/#unsafely-treat-insecure-origin-as-secure"  
enter the IP in the box