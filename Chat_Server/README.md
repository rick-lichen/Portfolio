[Back](../)

# Chat Server Web App
This is a Chat Server web app built from Node.js and Socket.io. This was an assignment from WashU's CSE330 course – Rapid Prototype Development and Creative Programming, and was done with my partner Josh Wang.

**Short Demo**

![Chat Server Demo](../Demos/Chat_Server_Demo.gif)

For the full demo video, watch on [Youtube](https://youtu.be/N0FixoygLeg)


**Functionalities**: 
- Users can create chat rooms with an arbitrary room name
- Users can join an arbitrary chat room
- The chat room displays all users currently in the room 
- A private room can be created that is password protected 
- Creators of chat rooms can temporarily kick others out of the room 
- Creators of chat rooms can permanently ban users from joining that particular room 
- A user's message shows their username and is sent to everyone in the room 
- Users can send private messages to another user in the same room


In addition to the basic functionalities, we decided to experiment with including **audio** to html via javascript and added an audio file to our instance. This is linked to the front-end so that whenever a new message is received, the notification sound is played. 

Finally, we decided to keep a log of the **chatlog** in the respective rooms. This way, any user that joined a room will have any previous conversation loaded into their chatlog section of the webpage. This was done through a pair of arrays associated with each individual room.

[See Code on GitHub](https://github.com/rick-lichen/Portfolio/edit/master/Chat_Server)
