# PHP Hackathon
This document has the purpose of summarizing the main functionalities your application managed to achieve from a technical perspective. Feel free to extend this template to meet your needs and also choose any approach you want for documenting your solution..

## Problem statement
*Congratulations, you have been chosen to handle the new client that has just signed up with us.  You are part of the software engineering team that has to build a solution for the new client’s business.
Now let’s see what this business is about: the client’s idea is to build a health center platform (the building is already there) that allows the booking of sport programmes (pilates, kangoo jumps), from here referred to simply as programmes. The main difference from her competitors is that she wants to make them accessible through other applications that already have a user base, such as maybe Facebook, Strava, Suunto or any custom application that wants to encourage their users to practice sport. This means they need to be able to integrate our client’s product into their own.
The team has decided that the best solution would be a REST API that could be integrated by those other platforms and that the application does not need a dedicated frontend (no html, css, yeeey!). After an initial discussion with the client, you know that the main responsibility of the API is to allow users to register to an existing programme and allow admins to create and delete programmes.
When creating programmes, admins need to provide a time interval (starting date and time and ending date and time), a maximum number of allowed participants (users that have registered to the programme) and a room in which the programme will take place.
Programmes need to be assigned a room within the health center. Each room can facilitate one or more programme types. The list of rooms and programme types can be fixed, with no possibility to add rooms or new types in the system. The api does not need to support CRUD operations on them.
All the programmes in the health center need to fully fit inside the daily schedule. This means that the same room cannot be used at the same time for separate programmes (a.k.a two programmes cannot use the same room at the same time). Also the same user cannot register to more than one programme in the same time interval (if kangoo jumps takes place from 10 to 12, she cannot participate in pilates from 11 to 13) even if the programmes are in different rooms. You also need to make sure that a user does not register to programmes that exceed the number of allowed maximum users.
Authentication is not an issue. It’s not required for users, as they can be registered into the system only with the (valid!) CNP. A list of admins can be hardcoded in the system and each can have a random string token that they would need to send as a request header in order for the application to know that specific request was made by an admin and the api was not abused by a bad actor. (for the purpose of this exercise, we won’t focus on security, but be aware this is a bad solution, do not try in production!)
You have estimated it takes 4 weeks to build this solution. You have 2 days. Good luck!*

## Technical documentation

!!!! Pentru rularea aplicatiiei prima data se va rula comanda: " php artisan migrate " iar mai apoi se va rula comanda " php artisan db:seed " deoarece in fisierul DatabaseSeeder.php am creat doi admini standard : 'admin1@gmail.com' si 'admin2@gmail.com' ambii avand parola: 'password'!!!
    
### Data and Domain model
In this section, please describe the main entities you managed to identify, the relationships between them and how you mapped them in the database.

Am gandit aplicatia ca avand o tablela de utilizatori, una ce va contine Programele si un ce va contine rezervarile pentru fiecare program in parte. 

Tabela de utilizatori va contine numele, emailul si parola

Tabela de Programe va contine numele programlui,ora de inceput, ora de sfarsit, numarul de paricipanti si camera in care se va desfasura programul

Tabela de rezervari va contine id utilizatorului daca acesta este conectat, CNP si id programului la care s-a facut programarea

### Application architecture
In this section, please provide a brief overview of the design of your application and highlight the main components and the interaction between them.

Aplicatie are doi admini standard care se vor putea conect in aplicatie si vor avea posibilitatea sa creeze programe noi si de a le sterge 

Utilizatori se vor putea inregistra si ei in aplicatie dar vor putea crea o rezervare doar daca introduc un CNP valid (acesta este necesar indiferent daca userul este conectat sau nu)
totodata utilizatorul va putea sterge o rezervare daca introduce CNP-ul si id programului.




###  Implementation
##### Functionalities
For each of the following functionalities, please tick the box if you implemented it and describe its input and output in your application:

[x] Brew coffee \

[X] Create programme 
->va primi numele programului , ora de inceput, ora de sfarsit si camera in care va avea loc
->va returna un mesaj in cazul in care o data introdusa nu trece de validare sau va returna programme-ul nou creat 

[X] Delete programme
->va primi id-ul programme-ului pe care doresc sa il sterg 
->va returna 1 in caz de succes si 0 in cazul in care programmue-ul este deja sters

[X] Book a programme
-> va primi CNP-ul utilizatorului, id-programului si daca este cazul id-ul utilizatorului 
-> va returna un mesaj in cazul in care datele nu au fost introduse corect sau va afisa programarea facuta

##### Business rules
Please highlight all the validations and mechanisms you identified as necessary in order to avoid inconsistent states and apply the business logic in your application.

Pentru ProgrammeController:

-in prima faza am restrictionat accesul inca din partea de rutare pentru rutele care apelau functii ce ar trebui sa fie accesibile doar pentru un utilizator atutentificat

-apoi am veificat ca acel utilizator autentificat sa fie unul dintre cei 2 admini pe care ii are aplicatia verificand daca flagul admin este "true"

-pentru fiecare tip de date intoduse am verificat sa respecte anumite standarde

-inainte de a crea un 'Programme' nou am verificat ca acestea sa nu se suprapuna cu alte Programe deja create



##### 3rd party libraries (if applicable)
Please give a brief review of the 3rd party libraries you used and how/ why you've integrated them into your project.


##### Environment
Please fill in the following table with the technologies you used in order to work at your application. Feel free to add more rows if you want us to know about anything else you used.
| Name | Choice |
| ------ | ------ |
| Operating system (OS) | e.g. Windows 10 Pro |
| Database  | e.g. MySQL |
| Web server| e.g. Apache |
| PHP | e.g. 7.4.12 |
| IDE | e.g. Visual Studio Code |
| Framework | Laravel 8.37.0 |

### Testing
In this section, please list the steps and/ or tools you've used in order to test the behaviour of your solution.

Pentru fiecare functie nou scrisa am testat folosindu-ma de Postman si introducand tot tipul de date care ar fi putul crea o eroare. 
In cazul in care ceva nu functiona ma intorceam la functia mea si utilizand return cautam sa vad de unde a plecat eroarea sau ce a facut ca functia  mea sa nu afizeze rezultatul asteptat.

## Feedback
In this section, please let us know what is your opinion about this experience and how we can improve it:

1. Have you ever been involved in a similar experience? If so, how was this one different?

Nu, nu am mai particiapat pana acum la un heckathone. Anul trecut am luat parte la un concurs ce presupunea carearea unei aplicatii la alegere tot in limbajul php doar ca aceia a avut timp de finalizare de 3 saptamani:) 

2. Do you think this type of selection process is suitable for you?

Da, genul acesta de proces de selectie se potriveste pentru mine deoarece se apropie foarte mult de ceia ce presupune munca de programator.

3. What's your opinion about the complexity of the requirements?

Pot spune ca pentru mine a avut o complexitate medie.

4. What did you enjoy the most?

Cel mai mult mi-a placut parta de validari.

5. What was the most challenging part of this anti hackathon?

Terminare aplicatiei in cele doua zile.

6. Do you think the time limit was suitable for the requirements?

Da. Aplicatia se poate realiza la un nivel decent in timpul cerut.

7. Did you find the resources you were sent on your email useful?

Nu m-am folosit de resursele trimise deoarece cunosteam in mare parte despre ce este vorba dar stiind ce aplicatii si tehnologii urmeaza sa folosesc m-a ajutat. 

8. Is there anything you would like to improve to your current implementation?

Pentru mine implementarea actuala a iesit destul de bine, cu timpul poate imi voi da seama unde ar mai trebui facute imbunatatiri.
Poate ar fi trebuit sa fac si un program zilnic si sa ferific ca adminul sa nu poata crea programme in afara oreleor dein program.

9. What would you change regarding this anti hackathon?

In conditiile in care nu ar fi pandemie cred ca as vrea ca acesta sa se desfasoare la sediul firmei sau alta locatie.



