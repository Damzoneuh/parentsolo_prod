import React, {Component} from 'react'
import axios from 'axios';
import Logger from "../../common/Logger";
import ReactDOM from 'react-dom';

export default class ResetEmail extends Component{
    constructor(props){
        super(props);
        let elem = document.getElementById('reset');
        this.state = {
            token: elem.dataset.csrf,
            email: null,
            message: null,
            type: null,
            trans: null
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount(){
        axios.get('/api/trans/all')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            })
    }

    handleChange(e){
        this.setState({
            email: e.target.value
        })
    }

    handleSubmit(){
        event.preventDefault();
        let data = {
            token: this.state.token,
            email: this.state.email,
        };
        axios.post('/reset', data)
            .then(res => {
                if (res.data.success){
                    this.setState({
                        type: 'success',
                        message: res.data.success
                    });
                    setTimeout(window.location.href = '/', 2000)
                }
                else {
                    this.setState({
                        type: 'error',
                        message: res.data.error
                    })
                }
            })
    }

    render() {
        const {type, message, trans} = this.state;
        if (trans){
            return (
                <div className="container">
                    <div className="row">
                        <div className="col">
                            <div className="testimony-wrap marg-top-50 marg-bottom-20">
                                <Logger type={type} message={message}/>
                                <form onChange={this.handleChange} onSubmit={this.handleSubmit} method="post">
                                    <div className="form-group text-center">
                                        <label htmlFor="email">{trans['talking.threat.sixth.red']}</label>
                                        <input type="email" name="email" className="form-control" required/>
                                    </div>
                                    <div className="marg-top-10 text-center">
                                        <button className="btn btn-group-lg btn-primary">{trans.validate}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
ReactDOM.render(<ResetEmail/>, document.getElementById('reset'));