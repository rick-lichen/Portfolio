import React, { Component } from "react";

class LoginRegister extends Component {
  constructor(props) {
    super(props);
    this.state = {
      username: "",
      password: "",
      registerClicked: false,
      loginClicked: true,
      loggedIn: false,
    };
  }
  handleChange(event) {
    this.setState({ [event.target.name]: event.target.value });
  }
  registerUser() {
    this.setState({ registerClicked: false });
    const data = {
      username: this.state.username,
      password: this.state.password,
    };
    const myHeader = new Headers();
    myHeader.append("Accept", "application/json");
    myHeader.append("Content-Type", "application/json");
    fetch("http://localhost:9000/register", {
      method: "POST",
      body: JSON.stringify(data),
      headers: myHeader,
    })
      .then((res) => res.json())
      .then((res) => {
        console.log(res);
        if (res.status !== 200) {
          alert(res.message.sqlMessage);
          console.log(res.message.sqlMessage);
        } else {
          console.log("Success:" + res.message);
          let defaultData = {
            username: this.state.username,
            bg: this.props.defaultBackColor,
            font: this.props.defaultFontColor,
            tasks: "",
          };
          fetch("http://localhost:9000/register/setDefault", {
            method: "POST",
            body: JSON.stringify(defaultData),
            headers: myHeader,
          })
            .then((res) => res.json())
            .then((result) => console.log(result))
            .catch((err) => console.log(err));
          alert(res.message);
        }
      })
      .catch((error) => console.error("Error:", error));
  }
  loginUser() {
    const data = {
      username: this.state.username,
      password: this.state.password,
    };
    const myHeader = new Headers();
    myHeader.append("Accept", "application/json");
    myHeader.append("Content-Type", "application/json");
    fetch("http://localhost:9000/login", {
      method: "POST",
      body: JSON.stringify(data),
      headers: myHeader,
    })
      .then((res) => res.json())
      .then((res) => {
        console.log(res);
        if (res.status !== 200) {
          alert(res.message);
          console.log(res.message);
        } else {
          console.log("Success:" + res.message[0]);
          alert(res.message[0]);
          let temp_1 = [];
          let temp_2 = [];
          for (
            let counter = 0;
            counter < res.message[3].length - 1;
            counter++
          ) {
            if (counter % 2 === 1) {
              temp_2 = temp_2.concat(res.message[3][counter]);
            } else {
              temp_1 = temp_1.concat(res.message[3][counter]);
            }
          }
          let passback = [
            this.state.username,
            res.message[0], //the login success message
            res.message[1],
            res.message[2],
            temp_1,
            temp_2,
          ];
          console.log("Logged in, the passback is " + passback);
          this.props.callbackFromApp(passback); //passing in the logged in username back to app
          this.setState({ loggedIn: true });
        }
      })
      .catch((error) => console.error("Error:", error));
  }
  registerCancel() {
    this.setState({ registerClicked: false, username: "", password: "" });
  }
  registerClicked() {
    this.setState({ registerClicked: true, username: "", password: "" });
  }
  logout() {
    this.setState({ loggedIn: false, username: "", password: "" }, () => {
      this.props.callbackFromApp([""]); //passing in logged out back to app (as an array to support the other callbacks)
    });
  }
  render() {
    return (
      <div className="login_register">
        {this.state.loggedIn ? ( //If logged in, don't show login_register
          <div>
            <button onClick={this.logout.bind(this)}>Log Out</button>
          </div>
        ) : (
          <div className ="notLoggedIn">
            {this.state.registerClicked ? ( //If register is clicked, change password field to text
            <div>
              <div className="input_field_lr">
                <input
                  type="text"
                  id="username"
                  name="username"
                  placeholder="Enter Username Here"
                  value={this.state.username}
                  onChange={this.handleChange.bind(this)}
                />
                <input
                  type="text"
                  id="password"
                  name="password"
                  placeholder="Enter Password Here"
                  value={this.state.password}
                  onChange={this.handleChange.bind(this)}
                />
                </div>
                <div className = "input_button_lr">
                  <button
                    id="register_submit"
                    onClick={this.registerUser.bind(this)}
                  >
                    Create Account
                  </button>
                  <button
                    id="register_cancel"
                    onClick={this.registerCancel.bind(this)}
                  >
                    Cancel
                  </button>
                </div>
              </div>
            ) : (
              <div>
                <div className="input_field_lr">
                  <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter Username Here"
                    value={this.state.username}
                    onChange={this.handleChange.bind(this)}
                  />
                  <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter Password Here"
                    value={this.state.password}
                    onChange={this.handleChange.bind(this)}
                  />
                  </div>
                  <div className = "input_button_lr">
                  <button id="login" onClick={this.loginUser.bind(this)}>
                    Login
                  </button>
                  <button id="register" onClick={this.registerClicked.bind(this)}>
                    Register
                  </button>
                </div>
              </div>
            )}
          </div>
        )}
      </div>
    );
  }
}
export default LoginRegister;
