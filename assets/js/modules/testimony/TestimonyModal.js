import React, {Component} from 'react';
import axios from 'axios';
import Logger from '../../common/Logger';

export default class TestimonyModal extends Component{
    constructor(props) {
        super(props);
        this.state = {
            message: null,
            type: null,
            text: null,
            title: null
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(e){
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    handleSubmit(e){
        e.preventDefault();
        let data = {
            text: this.state.text,
            title: this.state.title
        };
        axios.post('/api/testimony/add', data)
            .then(res => {
                this.setState({
                    message: res.data,
                    type: 'success'
                })
            });
        setInterval(() => {
            this.setState({
                message: null,
                type: null
            })
        }, 2000)
    }


    render() {
        const {trans} = this.props;
        const {message, type} = this.state;
        return (
            <form className="form" onChange={this.handleChange} onSubmit={this.handleSubmit}>
                {message && type ? <Logger message={message} type={type} /> : ''}
                <div className="form-group ">
                    <input type="text" name="title" required={true} placeholder={trans.title} className="form-control"/>
                </div>
                <div className="form-group">
                    <textarea required={true} name="text" placeholder={trans.description} className="form-control"/>
                </div>
                <div className="form-group d-flex justify-content-between align-items-center">
                   <button className="btn btn-group btn-success">{trans.validate}</button>
                </div>
            </form>
        );
    }

}