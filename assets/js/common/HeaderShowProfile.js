import React, {Component} from 'react';
import defaultMan from "../../fixed/HommeDefaut.png";
import defaultWoman from '../../fixed/FemmeDefaut.png';
import ImageRenderer from "./ImageRenderer";
import boyDefault from '../../fixed/GarconDefaut.png';
import girlDefault from '../../fixed/FilleDefaut.png';
import ChildImageRenderer from "./ChildImageRenderer";
import {library} from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {faComments, faSpa, faExclamationCircle, faHeart} from "@fortawesome/free-solid-svg-icons";
import TextAreaModal from "./TextAreaModal";
library.add(faComments, faSpa, faExclamationCircle, faHeart);
import axios from 'axios';
import MessageModal from "./MessageModal";
import ImgModal from "./ImgModal";
import ChildModal from "./ChildModal";
import Modal from "./Modal";
import Logger from "./Logger";

let el = document.getElementById('chat');
const es = new WebSocket('wss://ws.parentsolo.disons-demain.be/f/' + el.dataset.user);
const messageEs = new WebSocket('wss://ws.parentsolo.disons-demain.be/c/' + el.dataset.user);

export default class HeaderShowProfile extends Component {
    constructor(props) {
        super(props);
        this.state = {
            message: null,
            flowerText: null,
            flowerType: null,
            modalFlower: false,
            flowers: [],
            messagesModal: false,
            modal: false,
            selectedProfile: null,
            trans: null
        };
        this.handleClose = this.handleClose.bind(this);
        this.handleToggleFlowerModal = this.handleToggleFlowerModal.bind(this);
        this.handleSend = this.handleSend.bind(this);
        this.handleMessagesModal = this.handleMessagesModal.bind(this);
        this.handleAcceptModal = this.handleAcceptModal.bind(this);
        this.handleCloseModal = this.handleCloseModal.bind(this);
        this.handleFavorite = this.handleFavorite.bind(this);
        this.submitFavorite = this.submitFavorite.bind(this);
    }

    componentDidMount(){
        es.onopen = () => {};

        es.onclose = () => {
            es.onopen = () => {}
        };

        es.onerror = () => {
            es.onclose()
        };

        es.onmessage = message => {
            this.setState({
                message: JSON.parse(message.data)
            })
        };

        messageEs.onopen = () => {};

        messageEs.onclose = () => {
            messageEs.onopen()
        };

        messageEs.onerror = () => {
            messageEs.onclose()
        };

        messageEs.onmessage = message => {
            this.setState({
                message: JSON.parse(message.data)
            })
        };

        axios.get('/api/flowers')
            .then(res => {
                this.setState({
                    flowers: Object.entries(res.data)
                })
            });
        axios.get('/api/trans/all')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            })

    }

    handleClose(){
        this.setState({
            modalFlower: false,
            messagesModal: false
        })
    }

    handleToggleFlowerModal(){
        this.setState({
            modalFlower: true,
            messagesModal: false
        })
    }

    handleSend(value){
        if (value.action === 'flower'){
            axios.get('/api/flower/access/' + el.user)
                .then(res => {
                    if (res.data){
                        es.send(JSON.stringify({
                            action: 'flower',
                            target: this.props.profile.id,
                            message: value.text,
                            type: value.id
                        }));
                    }
                    else{
                        window.location.href='/shop';
                    }
                });
            this.setState({
                modalFlower: false,
                messagesModal: false
            })
        }
        else {
            messageEs.send(JSON.stringify({
                action: 'send',
                target: this.props.profile.id,
                message: value.message
            }));
            setTimeout(() => window.location.reload(), 2000)
        }
        this.setState({
            modalFlower: false,
            messagesModal: false
        })
    }

    handleMessagesModal(){
        this.setState({
            messagesModal: true,
            modalFlower: false
        })
    }

    handleCloseModal(){
        this.setState({
            modal: false
        })
    }

    handleFavorite(profile){
        if (profile.isFavorite){
            this.setState({
                selectedProfile: profile,
                action: 'favorite',
                modal: true
            })
        }
        else {
            this.submitFavorite(profile)
        }
    }

    submitFavorite(profile){
        axios.put('/api/favorite/' + profile.id)
            .then(res => {
                this.setState({
                    modal: false,
                    message: res.data
                })
            })
            .catch(e => {
                window.location.href ='/shop'
            })
    }

    handleAcceptModal(){
        this.submitFavorite(this.props.profile)
    }



    render() {
        const {profile} = this.props;
        const {flowers, modalFlower, messagesModal, modal, message, trans} = this.state;
        let count = 0;
        if (trans){
            return (
                <div className="banner-search d-flex flex-row justify-content-around align-items-center">
                    {/*{message ? <Logger message={message} type={'success'} /> : '' }*/}
                    {modalFlower && flowers.length > 0 ? <TextAreaModal handleClose={this.handleClose} handleSend={this.handleSend} flowers={flowers} validate={trans.validate} title={trans.flower}/> : ""}
                    {messagesModal ? <MessageModal handleClose={this.handleClose} handleSend={this.handleSend} validate={trans.validate} title={trans.message}/> : ''}
                    <div className="row marg-0 w-100 align-items-stretch">
                        {modal ? <Modal
                            text={trans['accept.favorite']}
                            type={'alert'}
                            title={trans.favorite}
                            handleClose={this.handleCloseModal}
                            handleAccept={this.handleAcceptModal}
                            validate={trans.validate}
                            cancel={trans.cancel}
                        /> : ''}
                        <div className="col-md-4 col-sm-12">
                            <div className="row">
                                {profile.img ?
                                    <div className={profile.img.length === 1 || profile.img.length === 0 ? "col-12" : "col-9"}>
                                        {profile.img.length > 0 ? profile.img.map(img => {
                                            if (img.isProfile) {
                                                return (<ImageRenderer id={img.img} alt={"profil-img"}
                                                                       className={"header-profile-img w-100"}/>)
                                            }
                                        }) : ''}
                                    </div> :
                                    <div className="col-12 text-center">
                                        {profile.isMan ?
                                            <img src={defaultMan} alt={"profile image"} className={"header-default-img "}/>
                                            :
                                            <img src={defaultWoman} alt={"profile image"} className={"header-default-img "}/>
                                        }
                                    </div>
                                }
                                {profile.img !== null && profile.img.length > 1 ?
                                    <div className="col-3">
                                        {profile.img.length <= 3 ? profile.img.map((img, key) => {
                                            if (!img.isProfile && key <= 2) {
                                                return (<ImageRenderer id={img.img} alt={"profil-img"}
                                                                       className={"header-profile-img w-100"}/>)
                                            }
                                        }) : ''}
                                        {profile.img.length > 3 ?
                                            profile.img.map(img => {
                                                if (!img.isProfile && count < 1){
                                                    count ++;
                                                    return (
                                                        <ImageRenderer id={img.img} alt={"profile image"} className={"header-profile-img w-100"}/>
                                                    )
                                                }
                                                if (!img.isProfile && count === 1){
                                                    count ++;
                                                    return (
                                                        <div className="position-relative marg-top-10">
                                                            <ImageRenderer id={img.img} alt={"profile image"} className={"header-profile-img w-100 marg-0"}/>
                                                            <div className="position-absolute header-absolute" data-toggle="modal"
                                                                 data-target=".bd-profile-modal-lg"> <a href="#" className="nav-link text-white">+ {profile.img.length - 3}</a></div>
                                                            <ImgModal img={profile.img} dataTarget={"bd-profile-modal-lg"}/>
                                                        </div>
                                                    )
                                                }
                                            }) : '' }
                                    </div>
                                    : ''}
                            </div>
                        </div>
                        <div className="col-md-5 col-sm-12 marg-top-10">
                            <h1>{profile.pseudo.toUpperCase()}</h1>
                            <h3>{profile.age} | {profile.city} - {profile.canton}</h3>
                            <h5>{profile.description}</h5>
                            <div className="d-flex flex-row justify-content-around">
                                <button className="btn btn-group btn-outline-danger marg-10" title={trans['flower.send']} onClick={this.handleToggleFlowerModal}><FontAwesomeIcon icon={faSpa} className={"badged-icons "}/>  </button>
                                <button className="btn btn-group btn-outline-dark marg-10" onClick={this.handleMessagesModal} title={trans['message.send']}> <FontAwesomeIcon icon={faComments} className={"badged-icons "} /> </button>
                                <a href="/contact" className="btn btn-group btn-outline-warning marg-10" title={trans['abuse_signal']}> <FontAwesomeIcon icon={faExclamationCircle} className={"badged-icons"} /> </a>
                                <button className="btn btn-group btn-outline-danger marg-10" title={trans['favorite.add']} onClick={() => this.handleFavorite(profile)}>
                                    <FontAwesomeIcon icon={faHeart} className={'badged-icons height-20'}/>
                                </button>
                            </div>
                        </div>
                        <div className="col-md-3 col-sm-12 marg-top-10">
                            <div className="row">
                                {profile.child ?
                                    <div className="col-12 text-center">
                                        <h3>{profile.child.length} {trans.child}</h3>
                                    </div>
                                    : ''}
                                {profile.child ?
                                    profile.child.map((child, key) => {
                                        if (child.img){
                                            if(key <= 1){
                                                return(
                                                    <div className="col-6">
                                                        <ChildImageRenderer child={child} alt={"child image"} className={"header-profile-img w-100 position-relative"}/>
                                                        <div className="child-age">{child.age} {trans['years.old']}</div>
                                                    </div>
                                                )
                                            }
                                        }
                                        else {
                                            return (
                                                <div className="col-6">
                                                    <img src={parseInt(child.sex) === 1 ? boyDefault : girlDefault} alt={"child image"} className={"header-profile-img w-100 position-relative"}/>
                                                    <div className="child-age">{child.age} {trans['years.old']}</div>
                                                </div>
                                            )
                                        }
                                    })
                                    : ''}
                                {profile.child && profile.child.length > 2 ?
                                    <div className="col-12 text-center marg-top-10">
                                        <button className="btn btn-group btn-outline-dark marg-10" data-toggle="modal"
                                                data-target=".bd-child-modal-lg"> {trans['view.more']}</button>
                                        <ChildModal child={profile.child} dataTarget={"bd-child-modal-lg"} trans={trans}/>
                                    </div> : '' }
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