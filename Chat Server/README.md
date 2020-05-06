# This is a Chat Server web app built from Node.js and Socket.io.

For the creative portion of the group assignment, we decided to implement an **invite function**. Users who are in a room other than the lobby will be able to get a list of the users in the lobby and invite them to join the room. The invite goes through the socket to the invited user which prompts a dialog with buttons accept or decline. The invited user can then choose to accept the invite and join the room. Having an invite will allow users to bypass the password prompting when joining a password-protected room. However, any user that was banned from the room cann not join the room even with an invite. 

In addition, we decided to experiment with including **audio** to html via javascript and added an audio file to our instance. This is linked to the front-end so that whenever a new message is received, the notification sound is played. 

Finally, we decided to keep a log of the **chatlog** in the respective rooms. This way, any user that joined a room will have any previous conversation loaded into their chatlog section of the webpage. This was done through a pair of arrays associated with each individual room.
