// Require the packages we will use:
let http = require("http"),
  socketio = require("socket.io"),
  fs = require("fs");

// Listen for HTTP connections.  This is essentially a miniature static file server that only serves our one file, client.html:
let app = http.createServer(function (req, resp) {
  // This callback runs when a new connection is made to our HTTP server.

  fs.readFile("client.html", function (err, data) {
    // This callback runs when the client.html file has been read from the filesystem.

    if (err) return resp.writeHead(500);
    resp.writeHead(200);
    resp.end(data);
  });
});
app.listen(3456);

let rooms = [["Lobby", null, null, null,[],[]]]; //Create Lobby as default, password is null, owner is null, ban-list is null, nothing in the chat-log
let user = {}; //Each user has a socket id as key, which corresponds to [0] = nickname, [1] = room the user's in
// Do the Socket.IO magic:
let io = socketio.listen(app);
io.sockets.on("connection", function (socket) {
  socket.join("Lobby");
  //join_room_func("Lobby");
  //socket.join("Lobby"); //Join lobby as default
  // This callback runs when a new Socket.IO connection is established.
  socket.on("message_to_server", function (data) {
    // This callback runs when the server receives a new message from the client.
    console.log("message: " + data["message"]); // log it to the Node.JS output
    console.log("current room: " + user[socket.id][1]);
    console.log("username: " + user[socket.id]);
    if (user[socket.id][1] == "Lobby") {
      console.log("Emitting message to everyone in lobby");
      rooms[0][4].push(user[socket.id][0]);
      rooms[0][5].push(data["message"]);
      io.emit("message_to_client", {
        name: user[socket.id][0],
        message: data["message"],
      }); // broadcast the message to other users
    } else {
      console.log("Emitting message to specific room");
      for (i in rooms){
        if (rooms[i][0]==user[socket.id][1]){
          rooms[i][4].push(user[socket.id][0]);
          rooms[i][5].push(data["message"]);
          console.log("currently the chat log in this room  is "+rooms[i][4]);
        }
      }
      io.to(user[socket.id][1]).emit("message_to_client", {
        name: user[socket.id][0],
        message: data["message"],
      }); // broadcast the message to other users
    }
  });
  socket.on("logout", function () {
    user[socket.id][0] = "";
    user[socket.id][1] = "";
      io.emit("disconnect");
  });
  function join_room_func(room) {
    let allowed = true;
    console.log("The user is currently supposedly in " + user[socket.id][1]);
    for (let i = 0; i < rooms.length; i++) {
      if (rooms[i][3] != null && rooms[i][0] == room) {
        //If room has a ban list
        for (let j = 0; j < rooms[i][3].length; j++) {
          if (rooms[i][3][j] == user[socket.id][0]) {
            //If user joining is on the ban list
            io.to(socket.id).emit("banned");
            allowed=false;
          }
        }
      }
    }
    if (allowed){
      socket.leave(user[socket.id][1], function (err) {
        //socket.leave is asynchronous, and we want the rest of the code to execute only after the user has left the original room.
          console.log("Leaving now, errors:" + err);
          for (r in socket.rooms) {
            console.log("The user is currently in " + r);
          }
        //Joins room
        socket.join(room, function (err) {
          //Same thing for joining, asynchronous, so we wait
        console.log("Joining room: " + room + "errors: " + err);
        let temp_old = user[socket.id][1];
        user[socket.id][1] = room; //Updates room with current room
        io.to(socket.id).emit("room_info_update", {
          current_room: user[socket.id][1],
        });
        
        for (i in rooms){
          if (rooms[i][0]==room){
            io.to(socket.id).emit("chat_history",{user_history:rooms[i][4],chat_history:rooms[i][5]});
          }
          if (rooms[i][2] == user[socket.id][0] && rooms[i][0] == room) {
            //console.log("You are the owner of the room!");
            //If user joining is the owner, activate owner powers
            io.to(socket.id).emit("owner");
          }
        }
        update_users(room);
        update_users(temp_old);
        });
      });
    }
  }
  function update_users(room_name){
    console.log(user[socket.id][0]+" requested an update on the room "+room_name);
    let nicknames = new Set();
    for (id in user) {
      if (user[id][1].localeCompare(room_name)==0) {
        nicknames.add(user[id][0]);
      }
    }
    console.log("trial2 "+room_name);
    nicknames = Array.from(nicknames);
    console.log(
      user[socket.id][0] +
        " is currently in the room " +
        user[socket.id][1] +
        " with " +
        nicknames
    );
    console.log(
      "users in the room " +
        room_name +
        " are updated with the list of users: " +
        nicknames
    );
    if (room_name == user[socket.id][1]) {
      io.in(room_name).emit("return_user_list", {
        users: nicknames,
        room: room_name,
        current_user: user[socket.id][0],
      });
    } else {
      socket.to(room_name).emit("return_user_list", {
        users: nicknames,
        room: room_name,
        current_user: user[socket.id][0],
      });
    }
    let in_lobby = new Set();
    for (id in user){
      if (user[id][1]=="Lobby"){
        in_lobby.add(user[id][0]);
      } 
    } 
    in_lobby=Array.from(in_lobby);
    console.log("Currently, "+in_lobby+" are in the looby");
    io.emit("return_lobby_users",{users:in_lobby});
  }
  socket.on("join_room", function (data) {
    console.log(
      data["user"] +
        "is attempting to join the room " +
        data["room_name"] +
        " while being " +
        data["invited"]
    );
    for (let i = 0; i < rooms.length; i++) {
      if ( 
        rooms[i][0] == data["room_name"] &&
        rooms[i][1] != null &&
        user[socket.id][0] == data["user"]&&!data["invited"]
      ) {
        //If room to join has a password and is the right user joining
        console.log("Attempting to join room with password");
        io.to(socket.id).emit("room_needs_password", {
          room: data["room_name"],
        });
      } else if (
        rooms[i][0] == data["room_name"] &&
        user[socket.id][0] == data["user"]
      ) {
        //socket.leave(user[socket.id[1]]); //Leaves current room first
        console.log("Joined room: " + data["room_name"]);
        join_room_func(data["room_name"]);
      }
    }
  });
  //When password is entered from joining a room
  socket.on("room_password_entered", function (data) {
    for (let i = 0; i < rooms.length; i++) {
      if (
        rooms[i][0] == data["room_name"] &&
        rooms[i][1] == data["pass"] &&
        user[socket.id][0] == data["user"]
      ) {
        //If room name matches and password matches and join user is correct
        console.log("password matches");
        join_room_func(data["room_name"]);
      } else if (rooms[i][0] == data["room_name"]) {
        io.to(socket.id).emit("password_incorrect", {
          room: data["room_name"],
        });
      }
    }
  });
  //Sets nickname
  socket.on("set_name", function (data) {
    user[socket.id] = [data["nickname"], "Lobby"]; //Logs user info into user dictionary
    join_room_func("Lobby");
    console.log(user[socket.id]);
  });
  //Updates current room
  socket.on("room_info", function () {
    io.to(socket.id).emit("room_info_update", {
      current_room: user[socket.id][1],
    });
    console.log("Current Room: " + user[socket.id][1]);
  });
  //Creates a room
  socket.on("create_room", function (data) {
    //looks for duplicate rooms based on room name
    console.log("create_room received");
    let duplicate_found = false;
    for (let i = 0; i < rooms.length; i++) {
      if (rooms[i][0] == data["room_name"]) {
        console.log("duplicate found");
        duplicate_found = true;
        io.to(socket.id).emit("duplicate_room", {
          room: data["room_name"],
        }); //duplicate room found, can't create
      }
    }
    console.log("out of for-loop, duplicate found is " + duplicate_found);
     //No duplicate entry, create room
    if (!duplicate_found) {
      let pass = data["room_pass"] == "" ? null : data["room_pass"]; //If password is empty, set it as null
      let temp_old = user[socket.id][1]; //If password is empty, set it as null
      rooms.push([data["room_name"], pass, user[socket.id][0], null,[],[]]); //Adds new room with name, password, owner, and ban-list = null
      console.log("No duplicate rooms are found, the room with the name "+data["room_name"]+" will be createad");
      join_room_func(data["room_name"]);
      // update_users(temp_old);
      // update_users(data["room_name"]);
    }
    io.to(socket.id).emit("create_room_done");
  });
  socket.on("send_inv",function(data){
    console.log(user[socket.id][0]+" is inviting "+ data["recipient"]+" to join "+user[socket.id][1]);
    for (id in user){
      if (user[id][0]==data["recipient"]){
        io.to(id).emit("pending_invite",{room:user[socket.id][1],inviter:user[socket.id][0]});
      }
    }
  });
  socket.on("inv_success",function(data){
    console.log(user[socket.id][0]+" has accepted the invite");
    for (id in user){
      if (user[id][0]==data["inviter"]){
        io.to(id).emit("inv_success_echo",{invited:user[socket.id][0]});
      }
    }
    //update_users("Lobby");
  });
  socket.on("inv_decline",function(data){
    console.log(user[socket.id][0]+" has declined the invite");
    for (id in user){
      if (user[id][0]==data["inviter"]){
        io.to(id).emit("inv_decline_echo",{invited:user[socket.id][0]});
      }
    }
  });
  //Gets list of current rooms 
  socket.on("room_list", function () {
    let output_array = []; //does not need to be a set since duplicate room names are not possible
    console.log("room_list was emitted from the client by "+user[socket.id][0]+" , below are the available rooms:");
    for (let i of rooms) {
      console.log(i);
      output_array.push(i[0]); //Add room name to output_array
    }
    io.to(socket.id).emit("room_list_return", { rooms: output_array });
  });
  socket.on("get_user", function () {
    update_users(user[socket.id][1]);
  });
  socket.on("private_message", function (data) {
    for (id in user) {
      if (user[id][0] == data["recipient"]) {
        io.to(id).emit("receive_private_message", {
          message: data["message"],
          sender: user[socket.id][0],
        });
      }
    }
  });
  //Received kick command for a user
  socket.on("kick", function (data) {
    console.log("kick received for " + data["user"]);
    for (id in user) {
      if (user[id][0] == data["user"]) {
        console.log("user " + [user[id][0]] + "id = " + id);
        io.to(id).emit("kicking_you");
      }
    }
  });
  //Received ban command
  socket.on("ban", function (data) {
    console.log("ban received for " + data["user"]);
    for (id in user) {
      if (user[id][0] == data["user"]) {
        //At this point id = id of user being banned
        for (let i = 0; i < rooms.length; i++) {
          if (rooms[i][0] == user[id][1] && rooms[i][3] == null) {
            //Adds user to ban-list (an array) of the room
            rooms[i][3] = [data["user"]];
            console.log("Ban list: " + rooms[i][3]);
          } else if (rooms[i][0] == user[id][1]) {
            rooms[i][3].push(data["user"]);
            console.log("Ban list: " + rooms[i][3]);
          }
        }
        io.to(id).emit("banning_you");
      }
    }
  });
  socket.on("bye", function () {
    console.log("bye received");
    socket.leave(user[socket.id][1]); //User leaves the current room
    console.log(
      user[socket.id][0] + " has been removed from " + user[socket.id][1]
    );
    join_room_func("Lobby");
  });
});
 