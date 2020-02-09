import React, {Component} from 'react';
import axios from 'axios';
import Logger from "../../common/Logger";
import ReactDOM from 'react-dom';

export default class Reset extends Component{
    constructor(props){
        super(props);
        let elem = document.getElementById('reset');
        this.state = {
            password: null,
            plainPassword: null,
            type: null,
            message: null,
            resetToken: elem.dataset.token,
            token: elem.dataset.csrf,
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
            [e.target.name] : e.target.value
        })
    }

    handleSubmit(){
        event.preventDefault();
        if (this.state.password === this.state.plainPassword) {
            let data = {
                password: this.state.password,
                plainPassword: this.state.plainPassword,
                token: this.state.token,
                resetToken: this.state.resetToken
            };
            axios.put('/api/reset', data)
                .then(res => {
                    if (res.data.success) {
                        this.setState({
                            type: 'success',
                            message: res.data.success
                        });
                        setTimeout(() => window.location.href = '/login', 2000)
                    } else {
                        this.setState({
                            type: 'error',
                            message: 'an error as been throw during the update'
                        })
                    }
                })
        }
        else {
            this.setState({
                type: 'error',
                message: 'Passwords not match . '
            })
        }
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
                                        <label htmlFor="password">{trans['talking.threat.seventh']}</label>
                                        <input type="password" name="password" className="form-control" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{6,22}$"/>
                                    </div>
                                    <div className="text-white font-size-minor text-center marg-bottom-10">{trans.pattern}</div>
                                    <div className="form-group text-center">
                                        <label htmlFor="plainPassword">{trans['talking.threat.seventh.confirm']}</label>
                                        <input type="password" name="plainPassword" className="form-control" required/>
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
        else{
            return (
                <div>

                </div>
            )
        }
    }
}

ReactDOM.render(<Reset/>, document.getElementById('reset'));