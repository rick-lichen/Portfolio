[Back](../)

# Focus Tree – Productivity Web App
This is a productivity web app created with the Forest mobile app as a source of inspiration. It uses React, Express, Node.js, MySQL, and SASS. This was an assignment from WashU's CSE330 course – Rapid Prototype Development and Creative Programming, and was done with my partner Josh Wang.

**Description**: Focus Tree is a productivity tool that allows users to set a timer for a period of time during which they will remain solely on the page. This allows the users to stay focused over a desired period of time thereby improving their efficiency when accomplishing tasks. The page will detect when the user switches tab or closes it altogether and break the timer that the user has initially set. However, the users are granted emergency breaks during which the timer will not break if they tab out (this could be useful for when the user needs to google something). In addition, the page allows users to create a to-do list with customizable times for each task. Finally, the page will play some default white-noise to help users concentrate but we will challenge ourselves to work with the Spotify API and allow users to connect to their personal Spotify playlists. 

**Short Demo**

![Focus Tree Demo](../Demos/Focus_Tree_Demo.gif)

For the full demo video, watch on [YouTube](https://youtu.be/h1tsfa73ZpA)

**Functionalities**:
- Register/Login/Guest User
- Users can create a timer
- Users can have custom to-do lists with custom times
- Detecting if our website is no longer the focus   - If not, terminate the study session
- Users can set an aesthetic theme with background music
- Custom music through Spotify API custom play list
- Emergency break to pause study session


# Creative Portion Description:
In addition to the basic functionalities, we also stored the logged in user's chosen theme as well as their remaining to-do lists. This was done by configuring a MySQL database in our Express backend. Registered users have their password hashed through Bcrypt and have a default theme and empty to-do list. When a user is logged in, any changes to the theme or to-do list in the front end would cause the backend to make the respective updates/inserts.
We also learned some animations in CSS and combined that with our newly learned SASS/SCSS framework. The logo would change depending on the timer progress and the input fields and buttons also have some animations as well. Altogether, we believe that we have met and even exceeded the 10-point allocation for our creative portion.

[See Code on GitHub](https://github.com/rick-lichen/Portfolio/edit/master/Focus_Tree)
