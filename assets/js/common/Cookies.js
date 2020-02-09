import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

export default class Cookies extends Component{
    constructor(props) {
        super(props);
        this.state = {
            trans: null,
            close: !!window.localStorage.getItem('cookies')
        };
        this.handleClick = this.handleClick.bind(this);
    }

    componentDidMount(){
        axios.get('/api/trans/all')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            })
    }

    handleClick(){
        this.setState({
            close: true
        });
        window.localStorage.setItem('cookies', true)
    }


    render() {
        const {trans, close} = this.state;
        if (trans && !close){
            return (
                <div className="position-fixed bottom-0 bg-cookies w-100 pad-30 text-center text-white">
                    <p>{trans['cookies.text']}</p>
                    <button onClick={this.handleClick} className="btn btn-group btn-outline-light">{trans['accept.cookies']} </button>
                </div>
            );
        }
        else {
            return (
                <div>

                </div>
            )
        }
    }
}

ReactDOM.render(<Cookies/>, document.getElementById('cookies'));