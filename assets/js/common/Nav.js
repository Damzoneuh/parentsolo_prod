import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {library} from '@fortawesome/fontawesome-svg-core';
import { faLockOpen, faLock } from "@fortawesome/free-solid-svg-icons";
import logo from '../../fixed/logo_noir.png';
import fr from '../../fixed/fr.png';
import de from '../../fixed/de.png';
import en from '../../fixed/en.png';
import LogoForLang from "./LogoForLang";


export default class Nav extends Component{
    constructor(props){
        super(props);
        library.add(faLockOpen, faLock);
        this.state = {
            lang: [],
            link: [],
            connection: [],
            flags: [],
            isLoaded: false,
            scroll: 0,
            toggle: false,
            phone: window.matchMedia('(max-width: 752px)').matches
        };

        this.handleLang = this.handleLang.bind(this);
        this.scrollHandler = this.scrollHandler.bind(this);
        this.toggle = this.toggle.bind(this);
    }

    componentDidMount(){
        axios.get('/api/nav')
            .then(res => {
                this.setState({
                    link : res.data.links,
                    connection : res.data.connection,
                    lang: res.data.lang,
                    isLoaded: true
                })
            });
        window.addEventListener('scroll', this.scrollHandler, true);
    }

    componentWillUnmount() {
        window.removeEventListener('scroll', this.scrollHandler);
    }

    handleLang(value){
        let data = {
            lang: value
        };
        if (value === 'de'){
            document.location.href = 'https://singleltern.ch' + document.location.pathname
        }
        else {
            axios.post("/api/lang", data)
                .then(res => {
                    if (res.data === 'ok'){
                        document.location.href = document.location.pathname
                    }
                })
        }
    }

    scrollHandler(){
        if (window.scrollY === 0){
            this.setState({
                scroll: 0
            })
        }
        else {
            this.setState({
                scroll: 1
            })
        }
    }

    toggle(){
        if (!this.state.toggle){
            this.setState({
                toggle: true
            })
        }
        else {
            this.setState({
                toggle: false
            })
        }
    }

    render() {
        const {isLoaded, lang, link, connection, scroll, toggle, phone} = this.state;
        if (isLoaded){
            return (
                <div className={scroll === 0 ? "" : "bg-light z fixed-top border-red-nav"}>
                    <nav className={!toggle ? "navbar navbar-expand-md navbar-light z" : "navbar navbar-expand-md navbar-light z bg-light border-red-nav"} >
                        <button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText"
                                aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation" onClick={this.toggle}>
                            <span className="navbar-toggler-icon"></span>
                        </button>
                        <div className="collapse navbar-collapse justify-content-between align-items-center" id="navbarText">
                            <ul className="navbar-nav ">
                                <li className="nav-item font-weight-bold"><a className="nav-link" href={link.home.path}>{link.home.name}</a></li>
                                <li className="nav-item font-weight-bold"><a className="nav-link" href={link.testimony.path}>{link.testimony.name}</a></li>
                                <li className="nav-item font-weight-bold"><a className="nav-link" href={link.faq.path}>{link.faq.name}</a></li>
                            </ul>
                            <LogoForLang alt="logo" className={scroll === 0 ? "none" : "nav-logo"} baseline={true} color={"black"} />
                            {!phone ? <div className={toggle ? "text-left" : "d-flex flex-row justify-content-center align-items-center"}>
                                <ul className="navbar-nav">
                                   <li className="nav-item">
                                       <a href={connection.path} className="nav-link custom-link font-weight-bold d-flex justify-content-between"><FontAwesomeIcon icon="lock-open" color={"rgba(0, 0, 0, 0.5)"} className={"pad-right-10"}/>
                                           <div className="custom-none" >{connection.name}</div></a></li>
                                </ul>
                                <ul className="navbar-nav">
                                    <li className="nav-item dropdown">
                                        <a className="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                                           role="button" data-toggle="dropdown" aria-haspopup="true"
                                           aria-expanded="false">
                                            {lang.selected === "fr" ? <span>Français <img src={fr} alt="flag" /></span> : '' }
                                            {lang.selected === "de" ? <span>Deutsch <img src={de} alt="flag" /></span> : '' }
                                            {lang.selected === "en" ? <span>English <img src={en} alt="flag" /></span> : ''}
                                        </a>
                                        {lang.selected === "fr" ?
                                            <div className="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                                <a className="dropdown-item" href="#" onClick={() => this.handleLang('de')}><span>Deutsch <img src={de} alt="flag" /></span></a>
                                                <a className="dropdown-item" href="#" onClick={() => this.handleLang('en')}><span>English <img src={en} alt="flag" /></span></a>
                                            </div>
                                         : ''}
                                        {lang.selected === "de" ?
                                            <div className="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                                <a className="dropdown-item" href="#" onClick={() => this.handleLang('fr')}><span>Français <img src={fr} alt="flag" /></span></a>
                                                <a className="dropdown-item" href="#" onClick={() => this.handleLang('en')}><span>English <img src={en} alt="flag" /></span></a>
                                            </div>
                                            : ''
                                        }
                                        {lang.selected === "en" ?
                                            <div className="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                                <a className="dropdown-item" href="#" onClick={() => this.handleLang('fr')}><span>Français <img src={fr} alt="flag" /></span></a>
                                                <a className="dropdown-item" href="#" onClick={() => this.handleLang('de')}><span>Deutsch <img src={de} alt="flag" /></span></a>
                                            </div>
                                            : ''
                                        }
                                    </li>
                                </ul>
                            </div> : ''}
                        </div>
                    </nav>
                    {phone ? <div className="d-flex flex-row justify-content-center align-items-center top-marg-nav position-fixed z">
                        <ul className="navbar-nav">
                            <li className="nav-item">
                            <a href={connection.path} className="nav-link custom-link font-weight-bold d-flex justify-content-between"><FontAwesomeIcon icon="lock-open" color={"rgba(0, 0, 0, 0.5)"} className={"pad-right-10"}/>
                                <div className="custom-none" >{connection.name}</div></a></li>
                        </ul>
                        <ul className="navbar-nav">
                            <li className="nav-item dropdown">
                                <a className="nav-link dropdown-toggle custom-link" href="#" id="navbarDropdownMenuLink"
                                   role="button" data-toggle="dropdown" aria-haspopup="true"
                                   aria-expanded="false">
                                    {lang.selected === "fr" ? <span>Français <img src={fr} alt="flag" /></span> : '' }
                                    {lang.selected === "de" ? <span>Deutsch <img src={de} alt="flag" /></span> : '' }
                                    {lang.selected === "en" ? <span>English <img src={en} alt="flag" /></span> : ''}
                                </a>
                                {lang.selected === "fr" ?
                                    <div className="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                        <a className="dropdown-item" href="#" onClick={() => this.handleLang('de')}><span>Deutsch <img src={de} alt="flag" /></span></a>
                                        <a className="dropdown-item" href="#" onClick={() => this.handleLang('en')}><span>English <img src={en} alt="flag" /></span></a>
                                    </div>
                                    : ''}
                                {lang.selected === "de" ?
                                    <div className="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                        <a className="dropdown-item" href="#" onClick={() => this.handleLang('fr')}><span>Français <img src={fr} alt="flag" /></span></a>
                                        <a className="dropdown-item" href="#" onClick={() => this.handleLang('en')}><span>English <img src={en} alt="flag" /></span></a>
                                    </div>
                                    : ''
                                }
                                {lang.selected === "en" ?
                                    <div className="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                        <a className="dropdown-item" href="#" onClick={() => this.handleLang('fr')}><span>Français <img src={fr} alt="flag" /></span></a>
                                        <a className="dropdown-item" href="#" onClick={() => this.handleLang('de')}><span>Deutsch <img src={de} alt="flag" /></span></a>
                                    </div>
                                    : ''
                                }
                            </li>
                        </ul>
                    </div> : ''}
                </div>
            )
        }
        return (
            <div>

            </div>
        );
    }

}
ReactDOM.render(<Nav />, document.getElementById('nav'));