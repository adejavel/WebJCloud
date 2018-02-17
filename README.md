# WebJCloud

## Introduction
WebJCloud is a web application to share and store documents. I actually use it to share documents with my family. 

## What it does
WebJCloud enables you to upload single or multiple files and to create folders sorted by year. It also integrate a carousel to view images and videos (not stable). You can then download the files and delete them. There is a mailer system that sends emails when there is new user and new files.

## Architecture
WebJCloud is based on a really simple asynchrone architecture with one major difference : there is a separated backend application to manage files. The architecture is : the frontend that communicates with the backend and the files applications, the backend that comunicates with the database and the files application that communicates with the backend to manage authorizations.

## Technologies

1. **FrontEnd :** Angular 4 with Bootstrap.
2. **BackEnd :** Symfony 3. 
3. **Files application :** PHP.
4. **Database :** MySQL database.


## Note
You can find here all the files in the `webjcloud_front/`, `webjcloud_back_symfo/`,`files_application/` folders to get all the source files. Then you can easily install them on a server/VM. You just have to change the differents URL of your application (front, back, files) to make them communicate :
1. Change `webjcloud_front/src/app/services/auth.service.ts : 11:12` to put the URL of your backend and tha file application.
2. Change `webjcloud_back_symfo/app/config/` files to put the correct database and mailer informations.
3. Change `webjcloud_back_symfo/src/AppBundle/Controller/ValidController/` to change the URL and the email address of the admin.
4. Change `webjcloud_back_symfo/src/AppBundle/Controller/UserController/` to change the URL and the email address of the admin.
5. Change `webjcloud_back_symfo/src/AppBundle/Controller/DocumentController/` to change the URL and the email address of the admin.
6. Change all the `files_application/` files to put the right API URL.
