Steps of installation
========================
Step 1. Below is the command to run the application:-

      - php bin/console -S ListProject -t web


Step 2. Go to the project directory. Install the comopser using below cmd:-

      - composer install

It will ask for the parameters for Database and Mailing details.For Emails you can use the below account details for quick testing.

mailer_host (127.0.0.1): smtp.gmail.com
mailer_user (null): rchoudhary16108@gmail.com
mailer_password (null): xkidlzmaawrxqmpv
secret (ThisTokenIsNotSoSecretChangeIt):


Step 3.Create the Database using below command:-

     - php bin/console doctrine:database:create
     

Step 4. Run migrations for creating tables using command:-

    - php bin/console doctrine:migrations:migrate

5.Go the the browser and run the application.
For example: if Listening on http://localhost:8000
Then Url will be: localhost:8000/app_dev/
