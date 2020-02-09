import React, {Component} from 'react';
import ReactDOM from 'react-dom';

let el = document.getElementById('toasts');
const es = new WebSocket('wss://ws.parentsolo.disons-demain.be/n/' + el.dataset.user);

export default class Toasts extends Component{
    constructor(props) {
        super(props);
        this.state = {
            flower: [],
            errors: null,
            message: null
        };
        this.connect = this.connect.bind(this);
        this.sortResponse = this.sortResponse.bind(this);
    }

    componentDidMount(){
        this.connect();
    }

    connect(){
        es.onopen = () => {

        };

        es.onmessage = res => {
            this.sortResponse(res);
        }
    }

    sortResponse(res){
        console.log(JSON.parse(res.data));
        let data = JSON.parse(res.data);
        if (data.redirect){
            window.location.href = '/' + res.data.redirect
        }
        if (typeof data.flowers !== 'undefined'){
            this.setState({
                flower: res.data.flowers
            })
        }
        //TODO data.success === success messages data.error === error message
    }

    render() {
        return (
            <div>

            </div>
        );
    }
}

ReactDOM.render(<Toasts/>, document.getElementById('toasts'));