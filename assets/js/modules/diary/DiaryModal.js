import React, {Component} from 'react';
import axios from 'axios';
import Logger from "../../common/Logger";

export default class DiaryModal extends Component{
    constructor(props) {
        super(props);
        let day = [];
        let month = [];
        let year = [];
        for (let i = 1; i < 12; i ++){
            month.push(i);
        }
        for (let i = 1; i < 31; i++){
            day.push(i)
        }
        for (let i = 2020; i < 2060; i++){
            year.push(i)
        }

        this.state = {
            day: day,
            month: month,
            year: year,
            selectedDay : 1,
            selectedMonth: 1,
            selectedYear: 2020,
            name: null,
            text: null,
            message: null,
            type: null,
            location: null
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
            day: this.state.selectedDay,
            month: this.state.selectedMonth,
            year: this.state.selectedYear,
            name: this.state.name,
            text: this.state.text,
            location: this.state.location
        };
        axios.post('/api/diary/add', data)
            .then(res => {
                this.setState({
                    message: res.data,
                    type: 'success'
                });
                setInterval(() => {
                    this.setState({
                        message: null,
                        type: null
                    })
                }, 2000)
            })
    }


    render() {
        const {trans} = this.props;
        const {day, month, year, message, type} = this.state;
        return (
            <form className="form" onSubmit={this.handleSubmit} onChange={this.handleChange}>
                {message && type ? <Logger message={message} type={type} /> : '' }
                <div className="form-group d-flex justify-content-between align-items-center">
                    <label htmlFor="name">{trans.name}</label>
                    <input type="text"  name="name" required={true}/>
                </div>
                <div className="form-group d-flex justify-content-between align-items-center">
                    <label htmlFor="location">{trans.location}</label>
                    <input type="text"  name="location" required={true}/>
                </div>
                <div className="form-group d-flex justify-content-between align-items-center">
                    <label htmlFor="selectedDay">{trans.day}</label>
                    <select name="selectedDay" required={true}>
                        {day.map(d => {
                            return (
                                <option value={d}>{d}</option>
                            )
                        })}
                    </select>
                </div>
                <div className="form-group d-flex justify-content-between align-items-center">
                    <label htmlFor="selectedMonth">{trans.month}</label>
                    <select name="selectedMonth"  required={true}>
                        {month.map(m => {
                            return (
                                <option value={m}>{m}</option>
                            )
                        })}
                    </select>
                </div>
                <div className="form-group d-flex justify-content-between align-items-center">
                    <label htmlFor="selectedYear">{trans.year}</label>
                    <select name="selectedYear"  required={true}>
                        {year.map(y => {
                            return (
                                <option value={y}>{y}</option>
                            )
                        })}
                    </select>
                </div>
                <div className="form-group d-flex justify-content-between align-items-center">
                    <label htmlFor="text">{trans.description}</label>
                    <textarea required={true}  name="text"></textarea>
                </div>
                <div className="form-group d-flex justify-content-between align-items-center">
                    <button className="btn btn-group btn-success">{trans.validate}</button>
                </div>
            </form>
        );
    }


}