import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import Logger from "../../common/Logger";

export default class Contact extends Component{
    constructor(props) {
        super(props);
        this.state = {
            trans: null,
            fields: null,
            text: null,
            email: null,
            service: null,
            log: null,
            type: null
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
            });
        axios.get('/api/contact/services')
            .then(res => {
                this.setState({
                    fields: res.data
                })
            })
    }

    handleChange(e){
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    handleSubmit(e){
        e.preventDefault();
        let data = {
            message: this.state.text,
            email: this.state.email,
            service: this.state.service
        };
        axios.post("/api/contact/send", data)
            .then(res => {
                this.setState({
                    log: res.data,
                    type: 'success'
                });
                setInterval(() => {
                    this.setState({
                        log: null,
                        type: null
                    });
                    window.location.href = '/'
                }, 4000)
            })
    }

    render() {
        const {trans, fields, type, log} = this.state;
        if (trans && fields){
            return (
                <div className="container-fluid">
                    {log && type ? <Logger type={type} message={log} /> : ''}
                    <div className="row">
                        <div className="col ">
                            <div className="d-flex justify-content-center align-items-center">
                                <div className="testimony-wrap pad-30 marg-bottom-20">
                                    <div className="text-center text-white marg-20"><h1>{trans.contact}</h1></div>
                                    <form className="form" onChange={this.handleChange} onSubmit={this.handleSubmit}>
                                        <div className="form-group">
                                            <select className="form-control" name={"service"} required={true}>
                                                <option value={null} defaultChecked={true}></option>
                                                {fields.map((field, key) => {
                                                    return (
                                                        <option key={key} value={field}>{trans[field]}</option>
                                                    )
                                                })}
                                            </select>
                                        </div>
                                        <div className="form-group">
                                            <label htmlFor="email" className="marg-right-10">Email</label>
                                            <input type="text" id="email" name="email" required={true}/>
                                        </div>
                                        <div className="form-group">
                                            <textarea className="form-control" name="text" required={true}></textarea>
                                        </div>
                                        <div className="text-center">
                                            <button className="btn btn-group btn-outline-light">{trans.validate}</button>
                                        </div>
                                    </form>
                                </div>
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
            );
        }
    }
}

ReactDOM.render(<Contact/>, document.getElementById('contact'));