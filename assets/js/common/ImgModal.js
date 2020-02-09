import React, {Component} from 'react';
import ImageRenderer from "./ImageRenderer";
import {library} from '@fortawesome/fontawesome-svg-core';
import {faArrowRight, faArrowLeft} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import axios from 'axios';
import Logger from "./Logger";
library.add(faArrowLeft, faArrowRight);

export default class ImgModal extends Component{
    constructor(props) {
        super(props);
        this.state = {
            target: 0,
            trans: null,
            message: null,
            type: null
        };

        this.nextImg = this.nextImg.bind(this);
        this.previousImg = this.previousImg.bind(this);
        this.handleChange = this.handleChange.bind(this);
    }

    componentDidMount(){
        axios.get('/api/trans/all')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            })
    }

    nextImg(){
        if (this.state.target + 1 === this.props.img.length){
            this.setState({
                target: 0
            })
        }

        else {
            this.setState({
                target: this.state.target + 1
            })
        }
    }

    previousImg(){
        if (this.state.target === 0){
            this.setState({
                target: this.props.img.length - 1
            })
        }
        else {
            this.setState({
                target: this.state.target - 1
            })
        }
    }

    handleChange(){
        let data = {
            img: this.props.img[this.state.target].img
        };
        axios.put('/api/img/setasprofile', data)
            .then(res => {
                this.setState({
                    message: res.data,
                    type: 'success'
                });
                setTimeout(() => this.setState({
                    message: null,
                    type: null
                }) , 2000);
                setTimeout(() => window.location.href === '/edit/profile', 2000)
            })
    }


    render() {
        const {target, trans, message, type} = this.state;
        const {img, dataTarget} = this.props;
        if (trans){
            return (
                <div>
                    {message && type ? <Logger message={message} type={type} /> : ''}
                    <div className={"modal fade " + dataTarget} tabIndex="-1" role="dialog"
                         aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div className="modal-dialog modal-lg">
                            <div className="modal-content">
                                <div className="modal-header">
                                    <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div className="d-flex align-items-center justify-content-between">
                                    <div onClick={this.previousImg}><FontAwesomeIcon icon={"arrow-left"} color={"rgb(0, 0, 0)"} className="font-size-30 marg-10"/></div>
                                    <ImageRenderer id={img[target].img} alt={"profile image"} className={"w-75"}/>
                                    <div onClick={this.nextImg}><FontAwesomeIcon icon={"arrow-right"} color={"rgb(0, 0, 0)"} className="font-size-30 marg-10"/></div>
                                </div>
                                {window.location.pathname === '/edit/profile' ?
                                    <form className="form text-center marg-top-20" onChange={this.handleChange}>
                                        <label className="form-check-label" htmlFor="is_profile">{trans['is.profile']}</label>
                                        <input type="checkbox" checked={img[target].isProfile} name="isProfile" id="is_profile"/>
                                    </form> : ''
                                }
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