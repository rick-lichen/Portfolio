var express = require("express");
var router = express.Router();
var mysql = require("mysql");
var bcrypt = require("bcrypt"); //Used for hashing password
var bodyParser = require("body-parser"); //to get post params

router.use(bodyParser.json()); // support json encoded bodies
router.use(bodyParser.urlencoded({ extended: true })); // support encoded bodies

//Create connection
const db = mysql.createConnection({
  host: "localhost",
  user: "330",
  password: "330",
  database:"tree",
});

db.connect((err) => {
  if (err) {
    throw err;
  }
  console.log("MySQL Connected");
});

router.post("/", function (req, res, next) {
  let username = req.body.username;
  let password = req.body.password;
  console.log("username: " + username + ", password : " + password);
  bcrypt.hash(password, 10, function (err, hash) {
    // Store hash in database
    let sql = "INSERT INTO users (username, password) values (?,?)";
    db.query(sql, [username, hash], (err, result) => {
      if (err) {
        console.error(err);
        res.status(err.statusCode || 500).json({ status: 500, message: err });
      } else {
        console.log(result);
        res.status(200).send({ status: 200, message: "User created" });
        // res.send("User successfuly registered!");
      }
    });
  });
});
router.post("/setDefault",function(req,res,next){
  let sql = "INSERT INTO user_settings (username,background_color,font_color,tasks) values (?,?,?,?)";
  db.query(sql,[req.body.username,req.body.bg, req.body.font,req.body.tasks],(err,result)=>{
    if (err){
      console.error(err);
      res.status(err.statusCode || 500).json({ status: 500, message: err });
    }
    else{
      console.log(result);
      res.status(200).send({ status: 200, message: "Default settings set for the user"});
    }
  });
})

module.exports = router;
