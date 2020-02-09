import React, {Component} from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {library} from '@fortawesome/fontawesome-svg-core';
import { faSortUp, faSortDown } from "@fortawesome/free-solid-svg-icons";
library.add(faSortDown, faSortUp);
import axios from 'axios';
import Logger from "../../common/Logger";

export default class DropDownProfileEdit extends Component{
    constructor(props) {
        super(props);
        this.state = {
            toggle: false,
            message: null,
            type: null
        };
        this.handleToggle = this.handleToggle.bind(this);
        this.handleSend = this.handleSend.bind(this);
    }

    handleToggle(){
        if (this.state.toggle){
            this.setState({
                toggle: false
            })
        }
        else {
            this.setState({
                toggle: true
            })
        }
    }

    handleSend(e){
        let data = {name: this.props.tableName, id: e.target.value};
        axios.put('/api/add/mtm', data)
            .then(res => {
                this.setState({
                    message: res.data,
                    type: "success"
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
        const {img, imgClass, title, table, trans, tableName, fieldName, user} = this.props;
        const {toggle, message, type} = this.state;
        return (
            <div>
                {message && type ? <Logger type={type} message={message} /> : ''}
                <div className="d-flex flex-row justify-content-between align-items-center">
                    <div className="d-flex align-items-end">
                        {img ? <img src={img} className={imgClass} alt={title} /> : '' }
                        <h4 className="marg-0 marg-left-10">{title}</h4>
                    </div>
                    <div onClick={this.handleToggle} >
                        {toggle ? <FontAwesomeIcon icon="sort-up" color={"rgb(0, 0, 0)"} className={"pad-right-10 font-size-30"}/> : <FontAwesomeIcon icon="sort-down" color={"rgb(0, 0, 0)"} className={"pad-right-10 font-size-30"}/>}
                    </div>
                </div>
                <div className={toggle ? "" : "none"}>
                    <form className="form" onChange={this.handleSend}>
                        {table ? table.map(field => {
                            let defaultChecked = false;
                            user[fieldName] ? user[fieldName].map(u => {
                                if (u.id === field.id){
                                    defaultChecked = true;
                                }
                            }) : '';
                            if (fieldName === 'lang'){
                                user.personality[fieldName] ? user.personality[fieldName].map(f => {
                                    if (f.id === field.id){
                                        defaultChecked = true;
                                    }
                                }) : '';
                            }
                            return (
                                <div className="d-flex justify-content-between">
                                    <label>{trans[field.name]}</label>
                                    <input type="checkbox" value={field.id} defaultChecked={defaultChecked}/>
                                </div>
                            )
                        }) : ''}
                    </form>
                </div>
            </div>
        );
    }


}