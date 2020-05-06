import React, { Component } from "react";
import {
  CircularProgressbarWithChildren,
  buildStyles,
} from "react-circular-progressbar";
import "react-circular-progressbar/dist/styles.css";

class Timer extends Component {
  constructor(props) {
    super(props);
    this.state = {
      display_sec: "",
      display_min: "",
      percentage: "",
    };
    this.seconds_left = 0;
    this.timer_duration = 0;
  }
  /* code modified from https://www.youtube.com/watch?v=NAx76xx40jM*/
  render() {
    const { display_sec } = this.state;
    const { display_min } = this.state;
    const { percentage } = this.state;
    return (
      <div className="Timer">
        <h1>
          <CircularProgressbarWithChildren
            className="ProgressBar"
            strokeWidth="6"
            counterClockwise={true}
            value={percentage}
            styles={buildStyles({
              height: "100px",
              width: "100px",
              textSize: "12px",
              trailColor: "#f7f7f7",
              pathColor: "#056644",
            })}
          >
            <strong id="TimerText">
              {display_min}:{display_sec}
            </strong>
          </CircularProgressbarWithChildren>
        </h1>
      </div>
    );
  }
  componentDidMount() {
    const { startMin } = this.props;
    this.seconds_left = startMin * 60;
    this.timer_duration = startMin * 60;
    this.setState({
      display_min: "Timer ",
      display_sec: " Starting",
    });
    this.myInterval = setInterval(() => {
      let m = Math.floor(this.seconds_left / 60);
      let s = this.seconds_left - m * 60;
      let per = (this.seconds_left / this.timer_duration) * 100;
      this.setState({
        display_min: m,
        display_sec: s,
        percentage: per,
      });
      //Some formatting
      if (s < 10) {
        this.setState({
          display_sec: "0" + s,
        });
      }
      if (m < 10) {
        this.setState({
          display_min: "0" + m,
        });
      }
      if ((m === 0) & (s === 0)) {
        alert("Time's up!");
        this.props.doneMethod();
        clearInterval(this.myInterval); //Stops timer
      }
      if (!this.props.inProgress) {
        this.seconds_left--;
        this.props.percentage(this.state.percentage);
      }
    }, 1000); //Every 1000ms, it'll render again
  }
  componentWillUnmount() {
    clearInterval(this.myInterval);
  }
}
export default Timer;
