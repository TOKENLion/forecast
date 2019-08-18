# PHP Technical Task
This simple application was written in `Yii2-Framework (Advanced)`. 

> *Note:* Application was destination for testing!!!

In the process of writing the application, the following technologies were used:
- PHP 7.2;
- PostgreSQL;
- Composer;
- Nginx 1.14;
- Open Server 5.3;

Instructions for started application:

1. Additional plugin has been installed:
    - PHP Db Seeder - https://github.com/tebazil/db-seeder
    - Queue Yii2 - https://github.com/yiisoft/yii2-queue/tree/master/docs/guide-ru
    - Yii2 DataTables - https://github.com/NullRefExcep/yii2-datatables
2. Initializing console migration using the command `php yii migrate/up`. List of migrations to be performed:
    - *m190809_184524_Create_Countries_Table.php*
    - *m190809_204910_Create_Cities_Table.php*
    - *m190809_205253_Create_Forecast_Table.php*
3. Run seed to generate data for the database from the console using the command `php yii seed/country-and-city`. 
Resource for seeds [https://simplemaps.com/data/world-cities](https://simplemaps.com/data/world-cities). File with data was saved on directory `resource/country_city`, 
name `worldcities.csv`.
4. Start job from console using the command `php yii job/get-forecast <city> <date start> <date end>`.
5. Run queues from console using the command `php yii queue/listen`.
6. Settings for Nginx you can see in the [file](/Nginx_1.14_vhost.conf) `Nginx_1.14_vhost.conf` 




