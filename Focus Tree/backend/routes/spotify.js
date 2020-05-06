var express = require("express");
var router = express.Router();
var request = require("request"); // "Request" library
var querystring = require("querystring");
var cookieParser = require("cookie-parser");
var spotApi = require("spotify-web-api-node");
router.use(cookieParser());
let name;
let userid;

// Code adapted from https://developer.spotify.com/documentation/general/guides/authorization-guide/
let my_client_id = "7d5e38331dd646abbbe4542d166caa8e";
let redirect_uri = "http://localhost:9000/spotify/authorized";
let client_secret = "fc6bd32a2f074e3f8a9161370456c73f";
let SpotifyWebApi  = new spotApi({
  cliendId:my_client_id,
  clientSecret:client_secret,
  redirectUri:redirect_uri
})

//Variables for when user's info is recorded
let user_id = "";

//Prompts user to log in
router.get("/login", function (req, res) {
  var scopes =
    "user-read-private user-read-email playlist-read-private playlist-read-collaborative";
  res.redirect(
    "https://accounts.spotify.com/authorize" +
      "?response_type=code" +
      "&client_id=" +
      my_client_id +
      (scopes ? "&scope=" + encodeURIComponent(scopes) : "") +
      "&redirect_uri=" +
      encodeURIComponent(redirect_uri)
  );
});

//On callback, user logged into Spotify
router.get("/authorized", function (req, res) {
  console.log("callback");
  let code = req.query.code || null; //	An authorization code that can be exchanged for an access token. Null if empty
  let error = req.query.error || null; //The reason authorization failed, for example: “access_denied”. Null if empty
  var authOptions = {
    url: "https://accounts.spotify.com/api/token",
    form: {
      code: code,
      redirect_uri: redirect_uri,
      grant_type: "authorization_code",
    },
    headers: {
      Authorization:
        "Basic " +
        new Buffer(my_client_id + ":" + client_secret).toString("base64"),
    },
    json: true,
  };
  request.post(authOptions, function (error, response, body) {
    if (!error && response.statusCode === 200) {
        console.log(body.access_token);
        let access_token = body.access_token;
        SpotifyWebApi.setAccessToken(body.access_token);
        refresh_token = body.refresh_token;

      var basic_info = {
        url: "https://api.spotify.com/v1/me",
        headers: { Authorization: "Bearer " + access_token },
        json: true,
      };

      // use the access token to access the Spotify Web API, gets basic info
      request.get(basic_info, function (error, response, body) {
        console.log(body);
        user_id = body.id;
        name= body.display_name
        console.log("user id = " + user_id);
        var playlist = {
          url: "https://api.spotify.com/v1/users/" + user_id + "/playlists",
          headers: { Authorization: "Bearer " + access_token },
          json: true,
        };
        request.get(playlist, function (error, response, body) {
            // console.log(body);
            // console.log("hello");
            if (body.items[1]!=null){
              // console.log("body.items: " + body.items[1]);
              // console.log("playlist name: " + body.items[1].name);
              // console.log("playlist id: " + body.items[1].id);
            }
        }); //gets playlist info
        res.redirect("http://localhost:3000");
      });
    } else {
      res.redirect(
        "/#" +
          querystring.stringify({
            error: "invalid_token",
          })
      );
    }
  });
});
// let data = {
//   code: code,
//   redirect_uri: redirect_uri, //same uri as above for validation
//   grant_type: "authorization_code",
// };
// const myHeader = new Headers();
// myHeader.append(
//   "Authorization",
//   "Basic " +
//     Buffer.from(my_client_id + ":" + client_secret).toString(
//       "base64"
//     ) /*Base 64 encoded string that contains the client ID and client secret key. The field must have the format: Authorization: Basic *<base64 encoded client_id:client_secret>* */
// );
// myHeader.append("Content-Type", "application/x-www-form-urlencoded");
// fetch("https://accounts.spotify.com/api/token", {
//   method: "POST",
//   body: data,
//   headers: {
//     myHeader,
//   },
// })
//   .then((res) => res.text())
//   .then((res) => {
//     console.log(res);
//   })
//   .catch((error) => console.error("Error:", error));
router.get("/loggedIn",function(req, res){
  if (name!=null){
    res.status(200).send({display_name:name});
  }
  else{
    res.send({message:"nope"});
  }
});
router.get("/getPlaylist",function(req,res){
  SpotifyWebApi.getUserPlaylists(user_id)
  .then(data=>{
    console.log(data.body);
    res.status(200).send(data.body);
  })
  .catch(err=>console.log(err));
});
module.exports = router;
