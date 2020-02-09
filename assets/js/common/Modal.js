import React, {Component} from 'react';
import LogoForLang from "./LogoForLang";

export default class Modal extends Component{
    constructor(props) {
        super(props);
        this.handleAccept = this.handleAccept.bind(this);
        this.handleClose = this.handleClose.bind(this);
    }

    handleClose(){
        this.props.handleClose()
    }

    handleAccept(){
        this.props.handleAccept()
    }


    render() {
        const {text, type, validate, cancel} = this.props;
        return (
            <div className="custom-modal">
                <div className="d-flex flex-row justify-content-end">
                    <button type="button" className="close" onClick={this.handleClose}>
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div className="text-center">
                    <LogoForLang color={'black'} baseline={true} />
                </div>
                <div className="text-center custom-modal-content">{text}</div>
                <div className="d-flex flex-row justify-content-around align-items-center">
                    <button className="btn btn-primary btn-group" onClick={this.handleAccept}>{validate}</button>
                    <button className={type === 'alert' ? 'btn btn-group btn-danger' : 'btn btn-group btn-success'} onClick={this.handleClose}>{cancel}</button>
                </div>
            </div>
        );
    }


}