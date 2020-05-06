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
  database: "tree",
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
  console.log("username" + username + "password" + password);
  let hash;
  let result_rows;
  let setting_result_rows;
  let getpw = "SELECT password FROM users WHERE username = ?";
  db.query(getpw, [username], (err, result) => {
    if (err) {
      console.error(err);
      res.status(err.statusCode || 500).json({ status: 500, message: err });
    } else {
      if (result.length > 0) {
        console.log(result);
        Object.keys(result).forEach(function (key) {
          result_rows = result[key]; //Binds result to result_rows
        });
        if (result_rows) hash = result_rows.password; //Stores password from database into hash
        bcrypt.compare(String(password), String(hash), function (
          err,
          compare_res
        ) {
          if (compare_res) {
            // Passwords match
            let getsettings =
              "SELECT background_color, font_color, tasks FROM user_settings WHERE username = ?";
            db.query(getsettings, [username], (err2, result2) => {
              if (err2) {
                console.log(err2);
                res
                  .status(err2.statusCode || 500)
                  .json({ status: 500, message: err2 });
              } else {
                Object.keys(result2).forEach(function (key) {
                  setting_result_rows = result2[key]; //Binds result to setting_result_rows
                });
                console.log("setting_result_rows: " + setting_result_rows);
                console.log(
                  "setting_result_rows.font_color: " +
                    setting_result_rows.font_color
                );
                console.log(
                  "setting_result_rows.background_color: " +
                    setting_result_rows.background_color
                );
                console.log("setting_result_rows.tasks "+setting_result_rows.tasks);
                let temp  = setting_result_rows.tasks.split('%/sep/%');
                res.status(200).send({
                  status: 200,
                  message: [
                    "User logged in successfully",
                    setting_result_rows.font_color,
                    setting_result_rows.background_color,
                    temp,
                  ],
                });
              }
            });
          } else {
            // Passwords don't match
            console.log(err);
            res.status(500).json({
              status: 500,
              message:
                "The password you've entered is wrong. Please try again.",
            });
          }
        });
      } else {
        //No query result == user does not exist
        res.status(500).json({
          status: 500,
          message: "This user does not exist. Please register first!",
        });
      }
    }
  });
  router.post("/save_settings", function (req, res, next) {
    let username = req.body.username;
    let bgColor = req.body.bgColor;
    let fontColor = req.body.fontColor;
    let tasks = req.body.tasks;
    let savetheme =
      "INSERT INTO user_settings (username, background_color, font_color, tasks) VALUES(?, ?, ?,?) ON DUPLICATE KEY UPDATE background_color=?, font_color = ?, tasks = ?";
    db.query(
      savetheme,
      [username, bgColor, fontColor, tasks,bgColor, fontColor,tasks],
      (err, result) => {
        if (err) {
          console.error(err);
          res.status(err.statusCode || 500).json({ status: 500, message: err });
        } else {
          console.log("User settings saved");
          res
            .status(200)
            .send({ status: 200, message: "User settings saved successfully" });
        }
      }
    );
  });

  //   bcrypt.hash(password, 10, function (err, hash) {
  //     // Store hash in database
  //     let sql = "INSERT INTO users (username, password) values (?,?)";
  //     db.query(sql, [username, hash], (err, result) => {
  //       if (err) {
  //         console.error(err);
  //         res.status(err.statusCode || 500).json({ status: 500, message: err });
  //       } else {
  //         console.log(result);
  //         res.status(200).send({ status: 200, message: "User created" });
  //         // res.send("User successfuly registered!");
  //       }
  //     });
  //   });
});

module.exports = router;
