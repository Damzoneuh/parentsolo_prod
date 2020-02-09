import React, {Component} from 'react';
import axios from "axios";
import card from '../../../fixed/card.png';


export default class CardEntries extends Component{
    constructor(props) {
        super(props);
        this.state = {
            isLoaded: true,
            number: null,
            month: 1,
            year: 2019,
            cvc: null,
            holder: null,
            item: this.props.item,
            trans: null
        };
        this.handleForm = this.handleForm.bind(this);
        this.handleFormSubmit = this.handleFormSubmit.bind(this);
        this.handleLogger = this.handleLogger.bind(this);
    }

    componentDidMount(){
        axios.get('/api/trans/all')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            })
    }
    handleForm(e){
        if (e.target.name === 'expiry'){
            this.setState({
                month: e.target.value.substr(0, 2),
                year: '20' +  e.target.value.substr(3, 5)
            });
        }
        else {
            this.setState({
                [e.target.name] : e.target.value
            })
        }
    }
    handleFormSubmit(e){
        e.preventDefault();
        this.setState({
            isLoaded: false
        });
        let data = {
            credentials: {},
            token: null,
            settings: {},
            item: this.props.item
        };
        data.credentials.number = this.state.number;
        data.credentials.holder = this.state.holder;
        data.credentials.year = this.state.year;
        data.credentials.month = this.state.month;
        data.credentials.cvc = this.state.cvc;
        data.token = this.props.token;
        data.settings.amount = this.props.settings.amount;
        data.settings.context = this.props.settings.context;
        data.settings.currency = this.props.settings.currency;
        axios.post('/api/card', data)
            .then(res => {
                let data = JSON.parse(res.data);
                console.log(data);
                if (data.error){
                    let log = {
                        message: data.error,
                        type: "error"
                    };
                    this.props.logger(log);
                    this.setState({
                        isLoaded: true
                    });
                }
                else {
                    if (data.Transaction.Status === 'AUTHORIZED'){
                        let data = {
                            id: this.state.item.id,
                            token: this.props.token
                        };
                        if (this.state.item.isSubscribe){
                            axios.post('/api/subscribe', data)
                                .then(res => {
                                    this.props.logger({message : 'Payment succeed, You will be logout to activate your subscription', type: 'success'});
                                    setTimeout(() => window.location.href = '/logout')
                                });
                        }
                        else {
                            this.props.logger({message : 'Payment succeed', type: 'success'});
                            setTimeout(() => window.location.href = '/dashboard')
                        }
                    }
                    else {
                        let log = {
                            message: 'An error was occurred during the payment',
                            type: "error"
                        };
                        this.handleLogger(log);
                        this.setState({
                            isLoaded: true
                        });
                    }
                }
            })
            .catch(e => {
                let log = {
                    message: 'An error was occurred during the payment',
                    type: "error"
                };
                this.handleLogger(log);
                this.setState({
                    isLoaded: true
                });
            })
    }
    handleLogger(log){
        this.props.logger(log);
    }
    render() {
        const {isLoaded, trans} = this.state;
        if (isLoaded && trans) {
            return (
                <div className="container">
                    <div className="w-50 box testimony-wrap padding-0">
                        <div className="bg-light w-100 text-center marg-top-10 marg-bottom-10"><img src={card} alt={"card"} className="card-img"/></div>
                        <div className="row pad-30">
                            <form onChange={this.handleForm} className="col-12" onSubmit={this.handleFormSubmit}>
                                <div className="row">
                                    <div className="form-group col-6 col-lg-6 ">
                                        <label htmlFor="number">{trans['card number']}</label>
                                        <input type="text" name="number" className="form-control" id="number"
                                               pattern="([0-9]){15,26}" required={true}/>
                                    </div>
                                    <div className="form-group col-6 col-lg-6">
                                        <label htmlFor="holder">{trans['full name']}</label>
                                        <input type="text" name="holder" className="form-control" id="holder" required={true}/>
                                    </div>
                                </div>
                                <div className="form-row align-items-center justify-content-around">
                                    <div className="form-group col-lg-2 col-md-6 col-12">
                                        <label htmlFor="month">{trans.month.charAt(0).toUpperCase() + trans.month.slice(1)}</label>
                                        <select className="form-control" id="month" name="month">
                                            <option value={1}>1</option>
                                            <option value={2}>2</option>
                                            <option value={3}>3</option>
                                            <option value={4}>4</option>
                                            <option value={5}>5</option>
                                            <option value={6}>6</option>
                                            <option value={7}>7</option>
                                            <option value={8}>8</option>
                                            <option value={9}>9</option>
                                            <option value={10}>10</option>
                                            <option value={11}>11</option>
                                            <option value={12}>12</option>
                                        </select>
                                    </div>
                                    <div className="form-group col-lg-2 col-md-6 col-12">
                                        <label htmlFor="year">{trans.year}</label>
                                        <select className="form-control" id="year" name="year">
                                            <option value={2019}>19</option>
                                            <option value={2020}>20</option>
                                            <option value={2021}>21</option>
                                            <option value={2022}>22</option>
                                            <option value={2023}>23</option>
                                            <option value={2024}>24</option>
                                            <option value={2025}>25</option>
                                            <option value={2026}>26</option>
                                            <option value={2027}>27</option>
                                            <option value={2028}>28</option>
                                            <option value={2029}>29</option>
                                            <option value={2030}>30</option>
                                        </select>
                                    </div>
                                    <div className="form-group col-lg-2 col-md-6 col-12 w-25">
                                        <label htmlFor="cvc">CVC</label>
                                        <input type="text" maxLength={3} className="form-control" name="cvc" id="cvc" required={true} pattern="\d{3}"/>
                                    </div>
                                </div>
                                <div className="container">
                                    <div className="row marg-top-10">
                                        <div className="col-lg-6 col-12 text-center">
                                            <button className="btn btn-group-lg btn-primary" type="submit">
                                                {trans.validate}
                                            </button>
                                        </div>
                                        <div className="col-lg-6 col-12 text-center ">
                                            <button className="btn btn-group-lg btn-primary"
                                                    onClick={() => this.props.handler(1)}>{trans.back}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            );
        }
        else {
            return (
                <div >

                </div>
            )
        }
    }
}