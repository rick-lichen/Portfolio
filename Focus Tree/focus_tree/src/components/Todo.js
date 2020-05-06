import React, { Component } from "react";

class Todo extends Component {
  constructor(props) {
    super(props);
    this.ref_Todo = React.createRef();
    this.state = {
      tasks: [],
      assoc_time: [],
      input_task: "",
      time_add: "",
      current_task: -1,
      on_focus: -1,
      progress: [],
    };
  }
  componentDidUpdate(prevProps, prevState) {
    if (prevProps.current !== this.props.current) {
      if (this.props.current) {
        console.log("we are setting current task to " + this.state.on_focus);
        this.setState({ current_task: this.state.on_focus });
      } else {
        console.log(
          "the Timer is not on from the perspective of Todo and current_task is " +
            this.state.current_task
        );
        if (this.state.current_task !== -1) {
          console.log("we are done with task # " + this.state.current_task);
          let temp = this.state.progress;
          temp[this.state.current_task] = 2;
          this.setState(
            { current_task: -1, on_focus: -1, progress: temp },
            () => this.props.updateTasks(this.getUnfinishedTasks())
          );
        }
      }
    }
    console.log(prevProps.loggedIn + " " + this.props.loggedIn);
    if (prevProps.loggedIn !== this.props.loggedIn) {
      if (this.props.existingTasks !== prevProps.existingTasks) {
        console.log(
          "from todo, existing tasks are " + this.props.existingTasks
        );
        console.log("existing tasks deteced");
        let temp = [];
        for (
          let counter = 0;
          counter < this.props.existingTasks[0].length;
          counter++
        ) {
          temp = temp.concat(0);
        }
        console.log("temp is " + temp);
        this.setState(
          {
            tasks: this.props.existingTasks[0],
            assoc_time: this.props.existingTasks[1],
            progress: temp,
          },
          () => {
            console.log(
              "progress is " +
                this.state.progress +
                "and the tasks for the logged in user are " +
                this.state.tasks
            );
          }
        );
      }
    }
  }
  render() {
    return (
      <div className="ToDoStuff">
        <strong className="ToDoMessage" style={{ color: this.props.fontColor }}>
          Here is a list of things you need to do!
        </strong>
        <div className="ListToDo">
          {this.state.tasks.map((item, index) => (
            <div key={index} onClick={() => this.handleClick(index)}>
              {this.checkList(item, index)}
            </div>
          ))}
        </div>
        <div className="InputTodo">
          <input
            className="todo_input"
            type="text"
            placeholder="Add todo..."
            name="input_task"
            value={this.state.input_task}
            style={{ width: "80%" }}
            onChange={this.handleChange.bind(this)}
          />
          <input
            className="todo_input"
            type="number"
            placeholder="Time"
            name="time_add"
            min="0"
            value={this.state.time_add}
            style={{ width: "20%", margin: "10px" }}
            onChange={this.handleChange.bind(this)}
          />
          <button
            id="todo_button"
            className="todo_button"
            onClick={this.addTodo}
          >
            Add
          </button>
        </div>
      </div>
    );
  }
  handleChange(event) {
    this.setState({ [event.target.name]: event.target.value });
  }
  handleClick(index) {
    if (this.props.current || this.state.progress[index] === 2) {
      console.log("Clicked when the timer is going");
    } else {
      this.props.addTimer(this.state.assoc_time[index], index);
      this.setState({ progress: this.updateStatus(index) });
      this.setState({ on_focus: index });
      console.log(this.state.progress[index]);
    }
  }
  checkList(item, index) {
    console.log(this.state.progress);
    if (this.state.progress[index] === 1) {
      return (
        <strong>
          {item} for {this.state.assoc_time[index]} minutes
        </strong>
      );
    } else if (this.state.progress[index] === 0) {
      return (
        <p>
          {item} for {this.state.assoc_time[index]} minutes
        </p>
      );
    } else {
      return (
        <strike>
          {item} for {this.state.assoc_time[index]} minutes
        </strike>
      );
    }
  }
  updateStatus(index) {
    let temp = this.state.progress;
    for (let counter = 0; counter < temp.length; counter++) {
      if (temp[counter] === 1) {
        temp[counter] = 0;
      }
    }
    temp[index] = 1;
    return temp;
  }
  addTodo = () => {
    if (this.state.input_task === "" || this.state.time_add === 0) {
      alert(
        "One of the inputs is not correct, please be sure to add a name or set a non-zero timer"
      );
    } else {
      this.setState(
        {
          tasks: this.state.tasks.concat(this.state.input_task),
          assoc_time: this.state.assoc_time.concat(this.state.time_add),
          progress: this.state.progress.concat(0),
        },
        () => this.props.updateTasks(this.getUnfinishedTasks())
      );
    }
  };
  getUnfinishedTasks = () => {
    let returnNames = this.state.tasks.slice();
    let returnTime = this.state.assoc_time.slice();
    for (let counter = 0; counter < this.state.progress.length; counter++) {
      if (this.state.progress[counter] === 2) {
        returnNames.splice(counter, 1);
        returnTime.splice(counter, 1);
      }
    }
    console.log(returnNames);
    return [returnNames, returnTime];
  };
}

export default Todo;
