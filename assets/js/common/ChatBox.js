import React, {Component} from 'react';
import axios from 'axios';
import ImageRenderer from "./ImageRenderer";
import defaultMan from "../../fixed/HommeDefaut.png";
import defaultWoman from "../../fixed/FemmeDefaut.png";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {library} from '@fortawesome/fontawesome-svg-core';
import { faSortUp, faCheck, faTimes} from "@fortawesome/free-solid-svg-icons";
import Smileys from "./Smileys";
library.add(faSortUp, faCheck, faTimes);
const el = document.getElementById('chat');

export default class ChatBox extends Component{
    constructor(props) {
        super(props);
        this.state = {
            fromInformation: [],
            wait: null,
            minimize: true,
            text: null,
            messages: this.props.messages
        };
        this.countWaitingMessages = this.countWaitingMessages.bind(this);
        this.handleRead = this.handleRead.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handlePayLoad = this.handlePayLoad.bind(this);
        this.handleClose = this.handleClose.bind(this);
    }

    componentDidMount(){
        axios.get('/api/profile/' + this.props.from)
            .then(res => {
                this.setState({
                    fromInformation: res.data
                });
                //console.log(res.data);
            });
        this.countWaitingMessages();
    }

    countWaitingMessages(){
        let wait = 0;
        this.props.messages.map(m => {
            if(!m.is_read && m.message_to === parseInt(el.dataset.user)){
                wait ++;
            }
        });
        this.setState({
            wait: wait
        })
    }

    handleRead(){
        if (this.state.minimize){
            let payload = {
                action: 'read',
                target: this.props.from
            };

            this.props.handlePayLoad(JSON.stringify(payload));
            this.countWaitingMessages();

            this.setState({
                minimize: false
            });
        }
        else {
            this.setState({
                minimize: true
            })
        }
    }

    handlePayLoad(e){
        e.preventDefault();
        let payLoad = {
            action: 'send',
            target: this.props.from,
            message: this.state.text
        };
        if (payLoad.message){
            this.props.handlePayLoad(JSON.stringify(payLoad));
            this.setState({
                text: null
            });
            let input = document.getElementById('chat-' + this.props.from);
            input.value = "";
        }
    }

    handleClose(){
        let payLoad = {
            action: 'close',
            target: this.props.from
        };
        this.props.handlePayLoad(JSON.stringify(payLoad))
    }

    handleChange(e){
        this.setState({
            text: e.target.value
        })
    }

    render() {
        const {fromInformation, wait, minimize, text} = this.state;
        const {messages, from} = this.props;
        if (Object.entries(fromInformation).length > 1){
            return (
                <div>
                    <div className="chat-top">
                        {typeof fromInformation.img !== 'undefined' && fromInformation.img !== null && fromInformation.img.length > 0 ?
                            fromInformation.img.map(img => {
                                if (img.isProfile){
                                    return (
                                        <div className="h-25 position-relative">
                                            <ImageRenderer id={img.img} alt={"profile image"} className={"chat-img position-absolute top-25"}/>
                                        </div>
                                    )
                                }
                            })
                            : typeof fromInformation === 'undefined' || fromInformation.img === null && fromInformation.isMan ?
                                <div className="h-25 position-relative">
                                    <img src={defaultMan} alt={"profil"} className={"chat-img position-absolute top-25 bg-light"}/>
                                </div>
                                : typeof fromInformation === 'undefined' || fromInformation.img === null && !fromInformation.isMan ?
                                    <div className="h-25 position-relative">
                                        <img src={defaultWoman} alt={"profil"} className={"chat-img position-absolute top-25 bg-light"}/>
                                    </div>
                                    : ''}

                        <div className="text-white marg-50">
                            {typeof fromInformation.pseudo !== 'undefined' ?
                                fromInformation.pseudo.toUpperCase() : ''}
                            <span className="text-dark"> | </span>
                            {typeof fromInformation.canton !== 'undefined' ? fromInformation.canton.toUpperCase().charAt(0) + '' + fromInformation.canton.toUpperCase().charAt(1) : ''}
                        </div>
                        <div className="flex-row d-flex align-items-center justify-content-around">
                            <div className={wait === 0 ? "chat-badge" : "chat-badge-new"}>
                                {wait}
                            </div>
                            <div className="marg-10">
                                <a onClick={this.handleRead}><FontAwesomeIcon icon={faSortUp} className={minimize ? "up" : "down"} color={"rgb(0,0,0)"} /></a>
                                <a className="marg-left-10" onClick={this.handleClose}><FontAwesomeIcon icon={faTimes} color={"rgb(0,0,0)"} /> </a>
                            </div>
                        </div>
                    </div>
                    <div className={minimize ? "minimize-box bg-light" : "bg-light w-100 chat-messages maximize-box"}>
                        {messages.map(message => {
                            //return (<div className={minimize ? "none" : "charset-utf"}>{decodeURI(message.content)}</div>)

                            if (message.message_from === parseInt(el.dataset.user)){
                                return (
                                    <div className={minimize ? "none" : "d-flex flew-row justify-content-end align-items-center"} >
                                        <div className={message.is_read ? "position-relative w-75 padding-0" : 'w-75 padding-0'}>
                                            <div className="pink-rounded">{decodeURI(message.content)}</div>
                                            {message.is_read ? <FontAwesomeIcon icon={faCheck} className={"is-read"} color={"rgb(0,255,0)"} />
                                                : ''}
                                        </div>
                                    </div>
                                )
                            }
                            else {
                                return(
                                    <div className={minimize ? "none" : "d-flex flex-row justify-content-start align-items-center"} >
                                        <div className="red-rounded w-75">{decodeURI(message.content)}</div>
                                    </div>
                                )
                            }
                        })}
                        <div className="anchor"> </div>
                    </div>
                    <div className={minimize ? "none" : 'bg-light d-flex flew-row justify-content-around align-items-center'}>
                        <form className="form" onSubmit={this.handlePayLoad}>
                            <div className="form-group marg-0 d-flex flex-row justify-content-between">
                                <label htmlFor={"chat-" + this.props.from}><Smileys element={"chat-" + this.props.from}/></label>
                                <input type="text" className="form-control" id={"chat-" + this.props.from } onChange={this.handleChange} value={this.state.text}/>
                            </div>
                        </form>
                    </div>
                </div>
            );
        }
        else {
            return (<div> </div>)
        }

    }

}