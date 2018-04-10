import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import Header from './Header';
 
/* An example React component */
class Main extends Component {
    render() {
        return (
            <div className="App">
                <Header />
                <p className="App-intro">
                  Welcome to the 'Movie Mojo' React app!
                </p>
            </div>
        );
    }
}
 
export default Main;
 
/* The if statement is required so as to Render the component on pages that have a div with an ID of "root";  
*/
 
if (document.getElementById('root')) {
    ReactDOM.render(<Main />, document.getElementById('root'));
}
