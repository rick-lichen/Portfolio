import React, { Component } from "react";
import Dropdown from "react-bootstrap/Dropdown";
import DropdownButton from "react-bootstrap/DropdownButton";

class ThemeChanger extends Component {
  constructor(props) {
    super(props);
    this.state = {
      color: "",
      colors: {
        Default: ["#282c34", "white"],
        Option1: ["#EAE7DC", "#E85A4F"],
        Option2: ["#D8C3A5", "black"],
        Option3: ["#8E8D8A", "white"],
        Option4: ["#E98074", "#EAE7DC"],
        Option5: ["#E85A4F ", "#EAE7DC"],
      },
    };
  }

  render() {
    const { colors } = this.state; // Essentially does: const vals = this.state.vals;
    return (
      <div className="ThemeChanger">
        <DropdownButton
          id="dropdown-basic-button"
          title="Change Background Color"
          drop="left"
        >
          {/*Generates a list of dropdown options for all colors defined above in state*/}
          {Object.keys(colors).map((key, index) => (
            <div key = {index}>
              <Dropdown.Item
                onClick={this.handleChange.bind(this)}
                style={{ color: this.props.fontColor, margin:"20px"}}
              >
                {key}
              </Dropdown.Item>
              <Dropdown.Divider />
            </div>
          ))}
        </DropdownButton>
      </div>
    );
  }

  handleChange(event) {
    this.setState(
      { color: this.state.colors[event.target.innerText] },
      () => this.props.callbackFromApp(this.state.color) //passing in the new background color back to app (in a callback to makesure it's executed once setstate is done)
    );
  }
}

export default ThemeChanger;
