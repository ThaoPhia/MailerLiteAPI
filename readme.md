## Setup
This application requires LAMP. So I assume you already have something setup on your local computer.  

* Download or pull this repository.
* Create a new database call "mailerite"
* Open file "mailerlite.sql", then copy/paste the query statements to create the tables.
* Open file "bootstrap.php", then change the database username & password accordingly.
* The application folder is where you need to point your config. Another option is to open a terminal and type the following code... This assume you have cd to project folder.

``` php -S 127.0.0.1:8000 -t application ``` 

## Client Page
The client page is the home page. You should be able to add subscriber and do a simple search.

## Postman
You should be able to use Postman to test this api. The authorization token is in bootsrap.php. Of course, this is not a real life project, so 
the location of these sensitive info should not be a problem. To save you time, I have included two files below.

* MailerLite.postman_collection.json
* MailerLite.postman_environment.json

 