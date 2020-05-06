import React from "react";
import "./App.scss";
import Timer from "./components/Timer";
import Todo from "./components/Todo";
import ThemeChanger from "./components/themeChanger";
import LoginRegister from "./components/loginRegister";
import ConfirmBox from "./components/confirmBox";
import Spotify from "./components/Spotify";

/*
function App() {
  return (
    <div className="App">
      <header className="App-header">
        <img src={logo} className="App-logo" alt="logo" />
        <p>Welcome to the Focus Tree!</p>
      </header>
    </div>
  );
}
*/
class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      apiResponse: "",
      min: 0,
      startClicked: false,
      bgColor: "#282c34",
      fontColor: "white",
      loggedInUser: "",
      emergencyTime: 0,
      paused: false,
      playList: "",
      tasks: [[], []],
      hidePlaylist: false,
      logoNum: 0,
      logoClass: "App-logo",
    };
  }
  /* code from https://www.freecodecamp.org/news/create-a-react-frontend-a-node-express-backend-and-connect-them-together-c5798926047c/*/

  // callAPI() {
  //   fetch("http://localhost:9000/test")
  //     .then((res) => res.text())
  //     .then((res) => this.setState({ apiResponse: res }));
  // }
  componentWillMount() {
    // this.callAPI();
  }
  componentDidMount() {
    window.addEventListener("focus", this.onFocus);
    window.addEventListener("blur", this.away);
  }
  componentWillUnmount() {
    window.removeEventListener("focus", this.away);
    window.addEventListener("blur", this.away);
  }
  onFocus = () => {
    //console.log("Tab is on focus");
  };
  away = () => {
    console.log("Tab is away");
    if (this.state.startClicked && !this.state.paused) {
      alert(
        "Oops. You went away from this page. Your study session has been reset :("
      );
      this.resetTimer();
    }
  };
  handleChange(event) {
    if (event.target.value >= 0) {
      this.setState({ [event.target.name]: event.target.value });
    } else {
      alert("Please enter a proper value");
    }
  }
  startTimer = () => {
    this.setState(
      {
        startClicked: true,
        logoClass: "App-logo",
      },
      console.log()
    );
  };
  resetTimer = () => {
    this.setState({
      startClicked: false,
      emergencyTime: 0,
    });
  };
  addToTimer = (timeToAdd, todo) => {
    this.setState({ min: Number(timeToAdd), emergencyTime: 0 });
    this.resetTimer();
    this.startTimer();
  };
  changeColor = (color) => {
    console.log(color);
    this.setState({ bgColor: color[0], fontColor: color[1] }, () =>
      this.saveUserSettings()
    );
  };
  loggedIn = (passback) => {
    console.log("Logged in username: " + passback[0]);
    console.log("passback: " + passback);
    if (passback[0] !== "") {
      this.setState(
        {
          loggedInUser: passback[0],
          fontColor: passback[2],
          bgColor: passback[3],
          tasks: [passback[4], passback[5]],
        },
        () => {
          console.log(
            "this user has these following tasks: " + this.state.tasks
          );
        }
      );
    } else {
      this.setState(
        {
          loggedInUser: passback[0],
          bgColor: "#282c34",
          fontColor: "white",
          tasks: [[], []],
        },
        () => {
          console.log("logged out");
          this.finished(); //stops timer if it was in progress
        }
      );
    }
    // console.log(passback[3]);
    // if (passback[3]!=""){
    //   let toProcess = passback[3];
    //   toProcess = toProcess.split ("%/sepTask%/");
    //   console.log(toProcess);
    //   toProcess=toProcess.split("%/sepTime%/");
    //   console.log(toProcess);
    // }
    this.saveUserSettings();
  };
  saveUserSettings() {
    console.log("saveUserSettings being called");
    console.log(this.state.tasks);
    if (this.state.loggedInUser !== "") {
      console.log("User is logged in. Attempting to save now...");
      console.log("Username is " + this.state.loggedInUser);
      //If user is logged in, save their data befor they leave
      let temp = "";
      for (let counter = 0; counter < this.state.tasks[0].length; counter++) {
        temp +=
          this.state.tasks[0][counter] +
          "%/sep/%" +
          this.state.tasks[1][counter] +
          "%/sep/%";
      }
      console.log(temp);
      const data = {
        bgColor: this.state.bgColor,
        fontColor: this.state.fontColor,
        username: this.state.loggedInUser,
        tasks: temp,
      };
      const myHeader = new Headers();
      myHeader.append("Accept", "application/json");
      myHeader.append("Content-Type", "application/json");
      fetch("http://localhost:9000/login/save_settings", {
        method: "POST",
        body: JSON.stringify(data),
        headers: myHeader,
      })
        .then((res) => res.json())
        .then((res) => {
          console.log(res);
          if (res.status !== 200) {
            console.log(res.message);
          } else {
            console.log("Success:" + res.message);
          }
        })
        .catch((error) => console.error("Error:", error));
    }
  }
  pauseTimer = () => {
    this.setState({ emergencyTime: this.state.emergencyTime + 1 });
    this.setState({ paused: true });
  };
  resumeTimer = () => {
    this.setState({ paused: false });
  };
  changePlaylist = (id) => {
    this.setState({
      playList: "https://open.spotify.com/embed/playlist/" + id,
      hidePlaylist: false,
    });
  };
  hidePlaylist = () => {
    this.setState({ hidePlaylist: true });
  };
  finished = () => {
    this.setState({ startClicked: false, logoClass: "App-logo-done" }, () => {
      this.saveUserSettings();
    });
  };
  updateTasks = (todo_Tasks) => {
    this.setState({ tasks: todo_Tasks }, () => this.saveUserSettings());
  };
  currentProgress = (percentage) => {
    if (percentage <= 20 && this.state.logoNum !== 4) {
      this.setState({ logoNum: 4 });
    } else if (
      percentage > 20 &&
      percentage <= 40 &&
      this.state.logoNum !== 3
    ) {
      this.setState({ logoNum: 3 });
    } else if (
      percentage > 40 &&
      percentage <= 60 &&
      this.state.logoNum !== 2
    ) {
      this.setState({ logoNum: 2 });
    } else if (
      percentage > 60 &&
      percentage <= 80 &&
      this.state.logoNum !== 1
    ) {
      this.setState({ logoNum: 1 });
    } else if (
      percentage > 80 &&
      percentage <= 100 &&
      this.state.logoNum !== 0
    ) {
      this.setState({ logoNum: 0 });
    }
  };

  render() {
    return (
      <div className="App" style={{ backgroundColor: this.state.bgColor }}>
        <header className="App-header">
          {/* <img src={logo} className="App-logo" alt="logo" /> */}
          <div className="TopContainer">
            {/* <div id = "SpaceFiller"></div> */}
            <img
              alt="Logo"
              src={require("./tree_icons/" + this.state.logoNum + ".png")}
              className={this.state.logoClass}
            />
            <div
              className="WelcomeMessage"
              style={{ color: this.state.fontColor }}
            >
              Welcome <span>{this.state.loggedInUser}</span> to the Focus Tree!
            </div>
            {/* login & registering fields */}
            <LoginRegister
              callbackFromApp={this.loggedIn}
              defaultBackColor={this.state.bgColor}
              defaultFontColor={this.state.fontColor}
              defaultTasks
            />
          </div>
          <div className="MiddleContainer">
            {/* <p style={{ color: this.state.fontColor }}>
            {this.state.apiResponse}
          </p> */}
            <Todo
              addTimer={this.addToTimer}
              current={this.state.startClicked}
              updateTasks={this.updateTasks}
              existingTasks={this.state.tasks}
              loggedIn={this.state.loggedInUser}
              fontColor={this.state.fontColor}
            />
            {/* {this.state.startClicked ? ( */}
            {/* // (<Todo addTimer = {this.addToTimer} currentTask = {this.state.currentTask} current ={false}/> )} */}
            <div className="TimerStuff">
              <p style={{ color: this.state.fontColor }}>Set a timer here!</p>
              {/*If timer hasn't start yet, allow user to set a timer. Disappears once timer started*/}
              {this.state.startClicked ? (
                <div></div>
              ) : (
                <div>
                  <input
                    id="timer"
                    type="number"
                    name="min"
                    style={{ width: "100px" }}
                    value={this.state.min}
                    min="0"
                    onChange={this.handleChange.bind(this)}
                  ></input>
                  <br></br>
                  <button onClick={this.startTimer}>Start Timer</button>
                </div>
              )}
              {this.state.startClicked ? (
                <div className="runningTimer">
                  <Timer
                    startMin={this.state.min}
                    inProgress={this.state.paused}
                    doneMethod={this.finished}
                    percentage={this.currentProgress}
                  />
                  <div className="otherTimerStuff">
                    <button onClick={this.resetTimer}>Reset Timer</button>
                    {this.state.emergencyTime < 3 ? (
                      <ConfirmBox
                        title={
                          "Emergency break #" + (this.state.emergencyTime + 1)
                        }
                        message={
                          "You now have used " +
                          (this.state.emergencyTime + 1) +
                          " out of 3 emergency breaks! Please click I'm done within 5 minutes."
                        }
                        yesLabel="I'm done"
                        yesMethod={this.resumeTimer}
                        button_name="Emergency Button"
                        addFunction={this.pauseTimer}
                        done={this.resetTimer}
                      />
                    ) : (
                      <div className="noEmergency">
                        No more emergency breaks!
                      </div>
                    )}
                  </div>
                </div>
              ) : (
                <div>
                  {/* <Timer startMin={this.state.min} inProgress = {this.state.paused} doneMethod = {this.finished}/> */}
                </div>
              )}
            </div>
            <div className="SpotifyStuff">
              <Spotify
                changePlaylist={this.changePlaylist}
                hidePlaylist={this.hidePlaylist}
                current={this.state.hidePlaylist}
              />
              {!this.state.hidePlaylist ? (
                <iframe
                  src={this.state.playList}
                  title="Spotify"
                  width="100%"
                  height="70%"
                  frameBorder="0"
                  allowtransparency="true"
                  allow="encrypted-media"
                ></iframe>
              ) : (
                <iframe
                  src={this.state.playList}
                  title="Spotify"
                  width="100%"
                  height="70%"
                  frameBorder="0"
                  allowtransparency="true"
                  allow="encrypted-media"
                  style={{ display: "none" }}
                ></iframe>
              )}
            </div>
          </div>
          <ThemeChanger
            callbackFromApp={this.changeColor}
            fontColor={this.state.fontColor}
          />
        </header>
      </div>
    );
  }
}

export default App;
