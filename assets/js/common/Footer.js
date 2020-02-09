import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import logo from '../../fixed/Logo_ParentsoloFR_Blanc.png';
import logoPostFinance from '../../fixed/Logo_PostFinance.png';
import fb from '../../fixed/Facebook.png';
import twit from '../../fixed/Twitter.svg';
import press1 from "../../fixed/20minuten.png";
import press2 from "../../fixed/AargauerZeitung.png";
import press3 from "../../fixed/Bilan.jpg";
import press4 from "../../fixed/RTS.jpg";
import press5 from "../../fixed/SchweizerIllustriert.jpg";
import press6 from "../../fixed/SRF.png";
import LogoForLang from "./LogoForLang";


export default class Footer extends Component{
    constructor(props){
        super(props);
        this.state= {
            isLoaded: false,
            links: [],
            press: [],
            user: []
        };
        axios.get('/api/press')
            .then(res => {
                this.setState({
                    press: res.data
                });
            });
        this.handleSub = this.handleSub.bind(this);
        this.handleShop = this.handleShop.bind(this);
        this.handleTestimony = this.handleTestimony.bind(this);
        this.handleRegister = this.handleRegister.bind(this);
    }
    componentDidMount(){
        axios.get('/api/footer')
            .then(res => {
               this.setState({
                   links: res.data,
               });
                axios.get('/api/user')
                    .then(res => {
                        this.setState({
                            user: res.data,
                            isLoaded: true
                        })
                    })
                    .catch(e => {
                        this.setState({
                            user: null,
                            isLoaded: true
                        })
                    })
            })
    }
    handleSub(){
        window.location.href='/shop'
    }

    handleRegister(){
        window.location.href='/'
    }

    handleShop(){
        window.location.href='/shop'
    }

    handleTestimony(){
        window.location.href='/testimony'
    }


    render() {
        const {isLoaded, links, press, user} = this.state;
        if (isLoaded){
            return (
                <div>
                    <div className="press-footer clearfix text-center">
                        <div><h2 className="float-left">{press.press}</h2></div>
                        <img className="float-left" src={press2} alt="press"/>
                        <img className="float-left" src={press3} alt="press"/>
                        <img className="float-left" src={press4} alt="press"/>
                        <img className="float-left" src={press1} alt="press"/>
                        <img className="float-left" src={press5} alt="press"/>
                        <img className="float-left" src={press6} alt="press"/>
                    </div>
                    <div className="footer-wrap">
                        <div className="row ext-row">
                            <div className="col-lg-6 col-md-6 col-sm-12">
                                <div className="flex flex-column justify-content-center align-items-center h-100 w-75 m-auto">
                                    <div>
                                        {user && !user.isSub ? <button className="btn btn-lg btn-success pulse" onClick={this.handleSub}>{links['subAndOption']}</button> : ''}
                                        {!user ? <button className="btn btn-lg btn-success pulse" onClick={this.handleRegister}>{links.sub}</button> : ''}
                                        {user && user.isSub && !user.isPremium ? <button className="btn btn-lg btn-success pulse" onClick={this.handleShop}>{links.goShop}</button> : ''}
                                        {user && user.isSub && user.isPremium ? <button className="btn btn-lg btn-success pulse" onClick={this.handleTestimony}>{links.letTestimony}</button> : ''}
                                    </div>
                                    <div className="bigger">
                                        <a href="/">{links.home}</a>|
                                        <a href="/diary">{links.diary}</a>|
                                        <a href="/faq">{links.faq}</a>|
                                        <a href="/testimony">{links.testimony}</a>|
                                        <a href="/contact">{links.contact}</a>
                                    </div>
                                    <div className="smaller ">
                                        <a href="/cgu">{links.cgu}</a>|
                                        <a href="/contact">{links.press}</a>|
                                        <a href="/contact">{links.add}</a>|
                                        <a href="/shop">{links.rate}</a>
                                        <div>© Parentsolo.ch - 2009 / 2020</div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-lg-6 col-md-6 col-sm-12">
                                <div className="row inner-row">
                                    <div className="col-xl-6 col-lg-12">
                                        <div className="flex flex-column justify-content-center align-items-center h-100">

                                            <div className="top-back text-center">
                                                {links.payment}
                                            </div>
                                            <img className="footer-logo-post" src={logoPostFinance} alt="logo"/>

                                        </div>
                                    </div>
                                    <div className="col-xl-6 col-lg-12">
                                        <div className="flex flex-column justify-content-center align-items-center h-100">
                                            <div className="text-center">
                                                <LogoForLang color={"white"} alt={"logo"} className="footer-logo" baseline={false}/>
                                            </div>
                                            <div className="flex flex-row justify-content-between align-items-center">
                                                <div className="follow">
                                                    {links.follow}
                                                </div>
                                                <a href="https://www.facebook.com/ParentsoloSingleltern/" target="_blank"><img src={fb} alt="facebook" className="follow-logo"/></a>
                                                <a href="https://twitter.com/parentsolo" target="_blank"><img src={twit} alt="twitter" className="follow-logo-twitter"/></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            );
        }
        else {return (<div></div>)}
    }
}
ReactDOM.render(<Footer/>, document.getElementById('footer'));