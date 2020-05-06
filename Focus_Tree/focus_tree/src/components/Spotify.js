import React, { Component } from "react";
class Spotify extends Component {
  constructor(props) {
    super(props);
    this.state = {
      loggedIn: false,
      nickname: "",
      playlists: null,
    };
  }
  componentDidMount() {
    const myHeader = new Headers();
    myHeader.append("Accept", "application/json");
    myHeader.append("Content-Type", "application/json");
    fetch("http://localhost:9000/spotify/loggedIn", {
      method: "GET",
      header: myHeader,
    })
      .then((res) => res.json())
      .then((result) => {
        if (result.display_name != null) {
          this.setState({ nickname: result.display_name, loggedIn: true });
          console.log("Retrieving playlists");
          fetch("http://localhost:9000/spotify/getPlaylist", {
            method: "GET",
            header: myHeader,
          })
            .then((res) => res.json())
            .then((result) => {
              console.log(result);
              this.setState({
                playlists: result.items.map((play_name) => (
                  <div
                    id={play_name.name}
                    key={play_name.name}
                    onClick={() => this.props.changePlaylist(play_name.id)}
                  >
                    {play_name.name}
                  </div>
                )),
              });
            });
        }
      })
      .catch((err) => console.log(err));
  }
  render() {
    return this.state.loggedIn ? (
      <div>
        <strong>Choose a playlist!</strong>
        <div className="SpotifyPlaylists">{this.state.playlists}</div>
        {!this.props.current ? (
          <button id="hidePlaylist" onClick={() => this.props.hidePlaylist()}>
            Hide playlist
          </button>
        ) : (
          <p></p>
        )}
      </div>
    ) : (
      <div className="SpotifyPlaylists">
        <a href="http://localhost:9000/spotify/login">
          Connect to your Personal Spotify!
        </a>
        <p>Default Study Music</p>
        <iframe
          src="https://open.spotify.com/embed/playlist/37i9dQZF1DX9sIqqvKsjG8"
          width="100%"
          title="Study Music"
          height="300%"
          frameborder="0"
          allowtransparency="true"
          allow="encrypted-media"
        ></iframe>
      </div>
    );
  }
}
export default Spotify;
