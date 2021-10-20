import React, {Component} from 'react';
import {render} from 'react-dom';

class UsersElement extends Component {
    render() {
        return <div className="card mx-auto w-25">
            <div className="card-subtitle">
                React App
            </div>
            <div className="card-text">
                <div>Back-end<br/>
                    <p className="text--secondary">Use ApiPlatform & Custom API made with Symfony 5</p></div>
            </div>
        </div>;
    }
}

render(<UsersElement/>, document.getElementById('user-list'));
