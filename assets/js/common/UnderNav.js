import React, {Component} from 'react';
import {library} from '@fortawesome/fontawesome-svg-core';
import {faBars, faComments, faUser, faSpa} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import ImageRenderer from "./ImageRenderer";
library.add(faBars, faComments, faUser, faSpa);
import axios from 'axios';
import defaultMan from '../../fixed/HommeDefaut.png';
import defaultWoman from '../../fixed/FemmeDefaut.png';

export default class UnderNav extends Component{
    constructor(props) {
        super(props);
        const el = document.getElementById(this.props.data);
        this.state = {
            isLoaded: false,
            display: el.dataset.display,
            isMan: el.dataset.isman,
            img: el.dataset.img,
            complete: el.dataset.complete,
            user: [],
            links: [],
            userElement: el.dataset.user,
            trans: null,
            parametersDropDown: false,
            userPayload: null
        };
        this.handleSub = this.handleSub.bind(this);
        this.handleShop = this.handleShop.bind(this);
        this.handleTestimony = this.handleTestimony.bind(this);
        this.handleLeave = this.handleLeave.bind(this);
        this.handleOver = this.handleOver.bind(this);
        this.handleRegister = this.handleRegister.bind(this);
    }

    componentDidMount(){
        const el = document.getElementById(this.props.data);
        axios.get('/api/user/' + el.dataset.user)
            .then(res => {
                this.setState({
                    user: res.data
                });
                axios.get('/api/footer')
                    .then(res => {
                        this.setState({
                            links: res.data
                        })
                    });
                axios.get('/api/trans/all')
                    .then(res => {
                        this.setState({
                            trans: res.data,
                            isLoaded: true
                        })
                    });
                axios.get('/api/profile/' + el.dataset.user)
                    .then(res => {
                        this.setState({
                            userPayload: res.data
                        })
                    });
            })
    }

    handleSub(){
        window.location.href='/shop'
    }

    handleShop(){
        window.location.href='/shop'
    }

    handleTestimony(){
        window.location.href='/testimony'
    }

    handleRegister(){
        window.location.href='/'
    }

    handleOver() {
        this.setState({
            parametersDropDown : true
        })
    }

    handleLeave(){
        this.setState({
            parametersDropDown: false
        })
    }

    render() {
        const {display, isMan, img, complete, user, isLoaded, links, trans, parametersDropDown, userPayload} = this.state;
        if (isLoaded && trans && userPayload){
            return (
                <div className="black-80 flex flex-row justify-content-between align-items-center">
                    <div className="d-flex flex-row align-items-center justify-content-center">
                        <ul className="navbar-nav marg-10">
                            <li className="nav-item dropdown">
                                <a className="nav-link " href="#" id="underNavDropdownMenuLink"
                                   role="button" data-toggle="dropdown" aria-haspopup="true">
                                    <FontAwesomeIcon icon={'bars'} color={"rgb(255,255,255)"} className={"marg-10 ham-under"}/>
                                </a>
                                <div className="dropdown-menu bg-danger text-white pad-10" aria-labelledby="UnderNavDropdownMenuLink">
                                    <a className="dropdown-item drop-down-white marg-bottom-10 marg-top-10" href="/" ><h5>{trans['home.link']}</h5></a>
                                    <a className="dropdown-item drop-down-white marg-bottom-10" href="/edit/profile"><h5>{trans['edit profile']}</h5></a>
                                    <div className="text-white nav-title"><h5 className="text-dark">{trans.search}</h5></div>
                                    <div className="dropdown-item drop-down-white marg-top-10 marg-bottom-10"><a className="border-bottom-black pad-bottom-10" href="/dashboard" >{trans['profil.search']}</a></div>
                                    <div className="dropdown-item drop-down-white marg-top-10 marg-bottom-10"><a className=" border-bottom-black pad-bottom-10" href="/favorite" >{trans['favorite.list']}</a></div>
                                    <div className="dropdown-item drop-down-white marg-top-10 marg-bottom-10"><a className=" pad-bottom-10" href="/dashboard" >Matching</a></div>
                                    <div className="text-white nav-title marg-top-20"><h5 className="text-dark">{trans.groups}</h5></div>
                                    <div className="dropdown-item drop-down-white marg-top-10 marg-bottom-10"><a className="border-bottom-black pad-bottom-10" href="/group" >{trans['groups.show.link']}</a></div>
                                    <div className="dropdown-item drop-down-white marg-top-10 marg-bottom-10"><a className=" border-bottom-black pad-bottom-10" href="/group/user" >{trans['my.groups']}</a></div>
                                    <div className="text-white nav-title marg-top-20"><h5 className="text-dark">{trans.diary}</h5></div>
                                    <div className="dropdown-item drop-down-white marg-top-10 marg-bottom-10"><a className="border-bottom-black pad-bottom-10" href="/diary" >{trans['diary.search']}</a></div>
                                    <div className="dropdown-item drop-down-white marg-top-10 marg-bottom-10 "><a className=" border-bottom-black pad-bottom-10" href="/diary" >{trans['diary.add']}</a></div>
                                    <div className="w-100 text-center"> <a href="/shop" className="marg-top-20 btn btn-group btn-outline-light">{trans['sub.and.option']}</a></div>
                                </div>
                            </li>
                        </ul>
                        {user && !user.isSub ? <button className="btn btn-lg btn-success pulse" onClick={this.handleSub}>{trans['sub.and.option']}</button> : ''}
                        {!user ? <button className="btn btn-lg btn-success pulse" onClick={this.handleRegister}>{links.sub}</button> : ''}
                        {user && user.isSub && !user.isPremium ? <button className="btn btn-lg btn-success pulse" onClick={this.handleShop}>{links.goShop}</button> : ''}
                        {user && user.isSub && user.isPremium ? <button className="btn btn-lg btn-success pulse" onClick={this.handleTestimony}>{links.letTestimony}</button> : ''}
                    </div>
                    <div className="w-50">
                        <ul className="navbar-nav flex flex-row justify-content-center align-items-center">
                            <li className="nav-item border-under-nav"><a href={"/conversations"} className="badged-icons border-ul-under" title={trans["my.conversations"]}><FontAwesomeIcon icon={"comments"} color={"rgb(255, 255, 255)"} /></a></li>
                            <li className="nav-item border-under-nav"><a href={"/visit"} className="badged-icons border-ul-under" title={trans["my.visits"]}><FontAwesomeIcon icon={"user"} color={"rgb(255, 255, 255)"} /></a></li>
                            <li className="nav-item"><a href={"/flowers"} className="badged-icons border-ul-under" title={trans['flower.received']}><FontAwesomeIcon icon={"spa"} color={"rgb(255, 255, 255)"} /></a></li>
                        </ul>
                    </div>
                    <a href={"/parameters"} onMouseOver={this.handleOver} onMouseLeave={this.handleLeave} className="position-relative" title={trans['account settings']}>
                    {img ? <ImageRenderer id={img} alt={"profil-img"} className={"testimony-img marg-10"} /> :
                        parseInt(isMan) === 1 ? <img src={defaultMan} alt="profil-img" className={"testimony-img marg-10"}/> : <img src={defaultWoman} alt="profil-img" className={"testimony-img marg-10"}/> }
                    </a>
                    <div className={parametersDropDown ? "position-absolute rounded-more border-red dropdown-parameters bg-light" : 'none'}>
                        <div className="text-center"><h4>{userPayload.pseudo.toUpperCase()}</h4></div>
                        <div className="text-center text-white bg-success marg-top-10">
                            {trans['parameter.error']}
                        </div>
                    </div>
                </div>
            );
        }
        else{
            return (<div></div>)
        }
    }
}