import { confirmAlert } from "react-confirm-alert";
import "react-confirm-alert/src/react-confirm-alert.css";
import React, { Component } from "react";
import Timer from "./Timer";
class ConfirmBox extends Component {
  constructor(props) {
    super(props);
    this.state = {
      done: false,
    };
    this.clickFunction = this.clickFunction.bind(this);
  }
  clickFunction() {
    this.props.addFunction();
    confirmAlert({
      title: this.props.title,
      message: this.props.message,
      buttons: [
        {
          label: this.props.yesLabel,
          onClick: this.props.yesMethod,
        },
      ],
      childrenElement: () => (
        <div>
          {this.state.done ? (
            <div></div>
          ) : (
            <Timer
              startMin={0.2}
              doneMethod={this.finished}
              inProgress={false}
              percentage={() => {}}
              doneMethod={() => {
                this.setState({ done: true }, () => {
                  this.props.done();
                });
              }}
            />
          )}
        </div>
      ),
      closeOnEscape: false,
      closeOnClickOutside: false,
    });
  }
  render() {
    return (
      <div className="confirm_box">
        <button onClick={this.clickFunction}>{this.props.button_name}</button>
      </div>
    );
  }
}
export default ConfirmBox;
