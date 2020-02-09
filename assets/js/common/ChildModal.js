import React, {Component} from 'react';
import ImageRenderer from "./ImageRenderer";
import {library} from '@fortawesome/fontawesome-svg-core';
import {faArrowRight, faArrowLeft} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import boyDefault from "../../fixed/GarconDefaut.png";
import girlDefault from "../../fixed/FilleDefaut.png";
library.add(faArrowLeft, faArrowRight);

export default class ImgModal extends Component{
    constructor(props) {
        super(props);
        this.state = {
            target: 0
        };

        this.nextImg = this.nextImg.bind(this);
        this.previousImg = this.previousImg.bind(this);
    }

    nextImg(){
        if (this.state.target + 1 === this.props.child.length){
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
                target: this.props.child.length - 1
            })
        }
        else {
            this.setState({
                target: this.state.target - 1
            })
        }
    }


    render() {
        const {target} = this.state;
        const {child, dataTarget, trans} = this.props;
        return (
            <div>
                <div className={"modal fade " + dataTarget} tabIndex="-1" role="dialog"
                     aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div className="modal-dialog modal-lg">
                        <div className="modal-content">
                            <div className="modal-header">
                                <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div className="text-center marg-50"><h3>{child[target].age} {trans.yearsOld ? trans.yearsOld : trans['years.old']}</h3></div>
                            <div className="d-flex align-items-center justify-content-between">
                                <div onClick={this.previousImg}><FontAwesomeIcon icon={"arrow-left"} color={"rgb(0, 0, 0)"} className="font-size-30 marg-10"/></div>
                                    {child[target].img ? <ImageRenderer id={child[target].img[0].id} alt={"profile image"} className={"w-75"}/> : ''}
                                    {!child[target].img ? <img src={parseInt(child.sex) === 1 ? boyDefault : girlDefault} alt={"child image"} className={"header-profile-img w-100 position-relative"}/> : ''}
                                <div onClick={this.nextImg}><FontAwesomeIcon icon={"arrow-right"} color={"rgb(0, 0, 0)"} className="font-size-30 marg-10"/></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }


}