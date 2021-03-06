import React, {Component} from 'react';
import axios from 'axios';
import ImageRenderer from "./ImageRenderer";
import Modal from '../common/Modal';
import defaultMan from '../../fixed/HommeDefaut.png';
import defaultWoman from '../../fixed/FemmeDefaut.png';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {library} from "@fortawesome/fontawesome-svg-core";
import {faHeart, faComments, faSpa, faUser} from "@fortawesome/free-solid-svg-icons";
import ProfilShow from "./ProfilShow";
import TextAreaModal from "./TextAreaModal";
import MessageModal from "./MessageModal";
import Pagination from "./Pagination";
library.add(faHeart, faComments, faUser, faSpa);
let el = document.getElementById('chat');
const es = new WebSocket('wss://ws.parentsolo.disons-demain.be/f/' + el.dataset.user);
const messageEs = new WebSocket('wss://ws.parentsolo.disons-demain.be/c/' + el.dataset.user);

export default class ShowAllProfile extends Component{
    constructor(props) {
        super(props);
        this.state = {
            search: this.props.search,
            profiles: [],
            modal: false,
            selectedProfile: {},
            trans: this.props.trans,
            modalFlower: false,
            flowers: [],
            transAll: null,
            selected: null,
            flowerGranted: false,
            messageGranted: false,
            displayed: null
        };
        this.handleFavorite = this.handleFavorite.bind(this);
        this.handleAcceptModal = this.handleAcceptModal.bind(this);
        this.handleCloseModal = this.handleCloseModal.bind(this);
        this.submitFavorite = this.submitFavorite.bind(this);
        this.handleSelectedProfile = this.handleSelectedProfile.bind(this);
        this.handleToggleFlowerModal = this.handleToggleFlowerModal.bind(this);
        this.handleMessagesModal = this.handleMessagesModal.bind(this);
        this.handleClose = this.handleClose.bind(this);
        this.handleSend = this.handleSend.bind(this);
        this.handleDisplayed = this.handleDisplayed.bind(this);
    }

    componentDidMount(){
        if (this.state.search.length > 0){
            let profiles = [];
            this.state.search.map(s => {
                axios.get('/api/profile/' + s)
                    .then(res => {
                        profiles.push(res.data);
                        this.pushProfiles(profiles);
                    })
            })

        }

        axios.get('/api/flower/access/' + el.dataset.user)
            .then(res => {
                if (res.data){
                    this.setState({
                        flowerGranted: true
                    })
                }
                else {
                    this.setState({
                        flowerGranted: false
                    })
                }
            });

        axios.get('/api/user/' + el.dataset.user)
            .then(res => {
                if(res.data.isSub){
                    this.setState({
                        messageGranted: true
                    })
                }
                else {
                    this.setState({
                        messageGranted: false
                    })
                }
            });


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
                    transAll: res.data
                })
            })
    }

    handleToggleFlowerModal(val){
        this.setState({
            modalFlower: true,
            messagesModal: false,
            selected: val.id
        })
    }

    handleSend(value){
        if (value.action === 'flower'){
            if (this.state.flowerGranted){
                es.send(JSON.stringify({
                    action: 'flower',
                    target: this.state.selected,
                    message: value.text,
                    type: value.id
                }));
            }
            else {
                window.location.href='/shop'
            }
        }
        else {
            messageEs.send(JSON.stringify({
                action: 'send',
                target: this.state.selected,
                message: value.message
            }))
        }
        this.setState({
            modalFlower: false,
            messagesModal: false,
            selected: null
        })
    }

    handleMessagesModal(val){
        if (this.state.messageGranted){
            this.setState({
                messagesModal: true,
                modalFlower: false,
                selected: val.id
            })
        }
        else{
            window.location.href='/shop'
        }

    }

    pushProfiles(value){
        this.setState({
            profiles: value
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
        let search = this.state.search;
        let profiles = [];
        axios.put('/api/favorite/' + profile.id)
            .then(res => {
                search.map(s => {
                    axios.get('/api/profile/' + s)
                        .then(res => {
                            profiles.push(res.data);
                            this.pushProfiles(profiles);
                            this.setState({modal: false})
                        })

                })
            })
            .catch(e => {
                window.location.href ='/shop'
            })
    }

    handleCloseModal(){
        this.setState({
            modal: false
        });
    }

    handleAcceptModal(){
        this.submitFavorite(this.state.selectedProfile)
    }

    handleSelectedProfile(id){
        this.props.handleShow(id);
        axios.post('/api/set/visit', {target: id})
            .then(res => {

            })
    }

    handleClose(){
        this.setState({
            modalFlower: false,
            messagesModal: false
        })
    }

    handleDisplayed(data){
        this.setState({
            displayed: data
        });
        console.log(data)
    }

    render() {
        const {profiles, modal, trans, modalFlower, messagesModal, flowers, transAll, displayed} = this.state;
        if (profiles.length > 0 && transAll){
            return (
                <div className="container-fluid">
                    {modalFlower && flowers.length > 0 ? <TextAreaModal handleClose={this.handleClose} handleSend={this.handleSend} flowers={flowers} validate={trans.validate} title={transAll.flower}/> : ""}
                    {messagesModal ? <MessageModal handleClose={this.handleClose} handleSend={this.handleSend} validate={trans.validate} title={transAll.message}/> : ''}
                    <div className={!modal ? 'none' : ''}>
                        <Modal
                        text={trans.acceptFavorite}
                        type={'alert'}
                        title={transAll.favorite}
                        handleClose={this.handleCloseModal}
                        handleAccept={this.handleAcceptModal}
                        validate={trans.validate}
                        cancel={trans.cancel}
                        />
                    </div>
                    <div className="row">
                        {displayed ? displayed.map(p => {
                            if (p){
                                return (
                                    <div className="col-lg-3 col-md-4 col-sm-12" key={p.id}>
                                        <div className="text-center rounded-more bg-light marg-10 pad-10">
                                            <div className="profile-all-wrap">
                                                {p.img && p.img.length > 0 ? p.img.map(img => {
                                                    if (img.isProfile){
                                                        return (
                                                            <ImageRenderer id={img.img} className={"thumb-profile-img rounded-more"} alt={"profile image"}/>
                                                        )
                                                    }
                                                }) : ''}
                                                {!p.img && p.isMan ? <img src={defaultMan} alt={"profile image"} className={"thumb-profile-img"}/> : ''}
                                                {!p.img && !p.isMan ? <img src={defaultWoman} alt={"profile image"} className={"thumb-profile-img"}/> : ''}
                                            </div>
                                            <h4 className="font-weight-bold">{p.pseudo.toUpperCase()}</h4>
                                            {p.age} | {p.city} - {p.canton}
                                            <div className="d-flex flex-row align-items-center justify-content-around marg-20">

                                                <button className="btn btn-outline-danger btn-lg btn-group-lg" onClick={() => this.handleSelectedProfile(p.id)}>{this.props.trans.view}</button>

                                                <a onClick={() => this.handleFavorite(p)} title={transAll['favorite.add']}>
                                                    <FontAwesomeIcon icon={'heart'} color={p.isFavorite ? 'rgba(255,0,0,0.8)' : 'rgba(0,0,0,0.3)'} className={'font-size-30'}/>
                                                </a>
                                                <a onClick={() => this.handleToggleFlowerModal(p)} title={transAll['flower.send']}><FontAwesomeIcon icon={'spa'} color={'rgba(0,0,0,0.3)'} className={'font-size-30'}/></a>
                                                <a onClick={() => this.handleMessagesModal(p)} title={transAll['message.send']}><FontAwesomeIcon icon={'comments'} color={'rgba(0,0,0,0.3)'} className={'font-size-30'}/></a>
                                            </div>
                                        </div>
                                    </div>
                                )
                            }
                        }) :
                        profiles.map(profile => {

                            return (
                                <div className="col-lg-3 col-md-4 col-sm-12" key={profile.id}>
                                    <div className="text-center rounded-more bg-light marg-10 pad-10">
                                        <div className="profile-all-wrap">
                                            {profile.img && profile.img.length > 0 ? profile.img.map(img => {
                                                if (img.isProfile){
                                                    return (
                                                        <ImageRenderer id={img.img} className={"thumb-profile-img rounded-more"} alt={"profile image"}/>
                                                    )
                                                }
                                            }) : ''}
                                            {!profile.img && profile.isMan ? <img src={defaultMan} alt={"profile image"} className={"thumb-profile-img"}/> : ''}
                                            {!profile.img && !profile.isMan ? <img src={defaultWoman} alt={"profile image"} className={"thumb-profile-img"}/> : ''}
                                        </div>
                                        <h4 className="font-weight-bold">{profile.pseudo.toUpperCase()}</h4>
                                        {profile.age} | {profile.city} - {profile.canton}
                                        <div className="d-flex flex-row align-items-center justify-content-around marg-20">

                                            <button className="btn btn-outline-danger btn-lg btn-group-lg" onClick={() => this.handleSelectedProfile(profile.id)}>{this.props.trans.view}</button>

                                            <a onClick={() => this.handleFavorite(profile)} title={transAll['favorite.add']}>
                                                <FontAwesomeIcon icon={'heart'} color={profile.isFavorite ? 'rgba(255,0,0,0.8)' : 'rgba(0,0,0,0.3)'} className={'font-size-30'}/>
                                            </a>
                                            <a onClick={() => this.handleToggleFlowerModal(profile)} title={transAll['flower.send']}><FontAwesomeIcon icon={'spa'} color={'rgba(0,0,0,0.3)'} className={'font-size-30'}/></a>
                                            <a onClick={() => this.handleMessagesModal(profile)} title={transAll['message.send']}><FontAwesomeIcon icon={'comments'} color={'rgba(0,0,0,0.3)'} className={'font-size-30'}/></a>
                                        </div>
                                    </div>
                                </div>
                            )
                        })}
                        <div className="col-12">
                            <Pagination data={profiles} handleDisplay={this.handleDisplayed} itemsPerPage={20} trans={transAll}/>
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