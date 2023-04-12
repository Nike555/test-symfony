We have a promotion for a client with prizes for users who play.

You have two tables with values for partners and prizes in a dual language system.
Your tasks are:

- import the data into a storage system of your choosing (we recommend MySQL);
- create a user persistence system (and acouple of users 5-10) in the same storage; (E.g. MySQL table)
	- users should be authenticated before they can play; 
	(no registration required, you can fill the data of the user in the persistence module you've chosen directly
	and use a secure authentication method of your choosing)
- the promotion will span 2 days and you will have to divide the prizes equally for the two days of the campaign; E.g.: if you have 100 prizes you have 50 prizes on the first day and 50 on the second.
- the game rules are the following:
	-the user logs in;
	-the user calls a service (a GET call to a url) to play the game and receives a random prize that is outputted;
	   (it is very important to deal with the concurrency issues so that the same prize is not awarded to two users and the exact amount of prizes is given and not more)
	-no prizes are to be given from 00:00:00 to 09:00:00 and 20:00:00 to 23:59:59;
	-the user can no longer play if he won a prize already that day;
	-if the user calls the play service again an error will be shown;
	-the user can call a service that tells him whether he played or not and if so then the prize will be outputted;
	-when the prize is outputted to the user it will also contain the partner data;
	-user will provide the language parameter to the server and server will choose appropriate language (for you to decide how to pass it);

The server must always reply in JSON format and the services should obey the REST principles.

We encourage you to use a framework and recommend the following which we are currently using: Symfony, Phalcon.
We look at database design, code design, OOP principles applied and code formatting.
