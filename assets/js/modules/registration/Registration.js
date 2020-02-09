import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import logo from '../../../fixed/Logo_ParentsoloFR_Noir_sansBL.png';
import TalkingThreatSubscribe from "./TalkingThreatSubscribe";
import press1 from '../../../fixed/20minuten.png';
import press2 from '../../../fixed/AargauerZeitung.png';
import press3 from '../../../fixed/Bilan.jpg';
import press4 from '../../../fixed/RTS.jpg';
import press5 from '../../../fixed/SchweizerIllustriert.jpg';
import press6 from '../../../fixed/SRF.png';
import LogoForLang from "../../common/LogoForLang";

export default class Registration extends Component{
    constructor(props){
        super(props);
        let phoneScreen = null;
        if (window.outerWidth > 752){
            phoneScreen = false;
            console.log(window.outerWidth)
        }
        else {
            phoneScreen = true;
            console.log(window.outerWidth)
        }
        this.state = {
            isLoaded: false,
            baseline: [],
            data: [],
            number: null,
            email: null,
            password: null,
            plainPassword: null,
            type: null,
            message: null,
            reset: null,
            isMan: false,
            activeImg: 2,
            press: null,
            phone: phoneScreen
        };
        window.matchMedia('(max-width: 752px)').matches;
        this.handleForm = this.handleForm.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleCheckbox = this.handleCheckbox.bind(this);
        this.CarouselHandler = this.CarouselHandler.bind(this);
    }

    componentDidMount(){
        axios.get('/api/baseline')
            .then(res => {
                this.setState({
                    isLoaded: true,
                    baseline: res.data
                })
            });
        axios.get('api/press')
            .then(res => {
                this.setState({
                    press: res.data
                })
            });
        if (window.matchMedia('(min-width: 768px)').matches){
            setInterval(() => this.CarouselHandler(), 60000)
        }
    }

    handleForm(e){
        if (e.target.type !== 'checkbox') {
            this.setState({
                [e.target.name]: e.target.value
            })
        }
    }

    handleSubmit(){
        event.preventDefault();
        if (
            this.state.password && this.state.plainPassword
            && this.state.email && this.state.number
        ){
            if (this.state.plainPassword === this.state.password){
                let elem = document.getElementById('register');
                let token = elem.dataset.token;
                let data = {};
                data.token = token;
                data.credentials = {};
                data.credentials.email = this.state.email;
                data.credentials.number = this.state.number;
                data.credentials.password = this.state.password;
                data.credentials.sex = this.state.isMan;
                axios.post('/api/register', data)
                    .then(res => {
                        this.setState({
                            message: 'An email was sent to ' + this.state.email + ' to confirm your profile',
                            type: 'success',
                            number: null,
                            email: null,
                            password: null,
                            plainPassword: null
                        });
                    });
            }
        }
    }

    handleCheckbox(){
        this.setState({
            isMan: !this.state.isMan
        })
    }

    CarouselHandler(){
        let active = this.state.activeImg;
        if (active === 3){
            active = 1
        }
        else {
            active = active + 1
        }
        this.setState({
            activeImg: active
        })
    }

    render() {
        const {isLoaded,baseline, activeImg, press, phone} = this.state;
        if (!isLoaded || !press )
        {
            return (
                <div></div>
            )
        }
        else {
            return (
                    <div className="register-wrap">
                        {!phone ?
                            <div className={"w-100 banner banner-" + activeImg}>
                                <div className="row row-banner">
                                    <div className="offset-lg-6 col-lg-6 col-12 text-center marg-top-50">
                                        <LogoForLang alt={"logo"} color={"black"} baseline={false} className={"w-75"} />
                                        <div className="flex-row d-flex justify-content-center align-items-center">
                                            <h1 className="w-75 baseline">{baseline.baseline[0]} <span className="threat-red">{baseline.baseline[1]}</span></h1>
                                        </div>
                                        <TalkingThreatSubscribe phone={phone}/>
                                    </div>
                                </div>
                            </div>
                            :
                            <div className={"w-100 banner banner-phone"}>
                                <div className="row row-banner">
                                    <div className="offset-lg-6 col-lg-6 col-12 text-right marg-top-50">
                                        <LogoForLang alt={"logo"} color={"black"} baseline={false} className={"w-75"} />
                                        <div className="flex-row d-flex justify-content-end align-items-center">
                                            <h1 className="w-75 baseline">{baseline.baseline[0]} <span className="threat-red">{baseline.baseline[1]}</span></h1>
                                        </div>
                                        <TalkingThreatSubscribe isPhone={phone}/>
                                    </div>
                                </div>
                            </div>
                        }
                        <div className="press flex flex-row align-items-center justify-content-around">
                            <h2>{press.press}</h2>
                            <img src={press1} alt="press"/>
                            <img src={press2} alt="press"/>
                            <img src={press3} alt="press"/>
                            <img src={press4} alt="press"/>
                            <img src={press5} alt="press"/>
                            <img src={press6} alt="press"/>
                        </div>
                    </div>
            )
        }
    }
}
ReactDOM.render(<Registration />, document.getElementById('register'));
