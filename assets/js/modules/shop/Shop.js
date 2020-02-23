import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {library} from '@fortawesome/fontawesome-svg-core';
import { faCheck, faTimes } from "@fortawesome/free-solid-svg-icons";
library.add(faCheck, faTimes);

export default class Shop extends Component{
    constructor(props){
        super(props);
        this.state = {
            isLoaded: false,
            trans: [],
            items: null,
            selected: null
        };
        this.handleShop = this.handleShop.bind(this);
        this.handleChange = this.handleChange.bind(this);
    }

    componentDidMount(){
        axios.get('/api/shop')
            .then(res => {
                this.setState({
                    trans: res.data
                });
                axios.get('/api/items')
                    .then(res => {
                        console.log(res.data);
                        this.setState({
                            isLoaded: true,
                            items: res.data
                        })
                    });
            });
    }

    handleChange(e){
        this.setState({
            selected: e.target.value
        })
    }

    handleShop(e){
        e.preventDefault();
        if (!this.state.selected){
            if (e.target.id === 'sub'){
                window.location.href = '/payment/2';
            }
            else {
                window.location.href = '/payment/1';
            }
        }
        else {
            window.location.href = '/payment/' + this.state.selected;
        }
    }

    render() {
        const {isLoaded, trans, items} = this.state;
        if (isLoaded && items && items.length > 0){
            let selected = false;
            return (
                <div className="container-fluid">
                    <div className="row m-auto">
                        <div className="border-red rounded-more pad-30 text-center col-md-6 col-sm-12 marg-top-10 marg-bottom-20">
                            <h1>{trans.sub}</h1>
                                <form onChange={this.handleChange} onSubmit={this.handleShop} className="d-flex flex-row justify-content-between" id="sub">
                                    <select>
                                        {items.map((item) => {
                                            if (item.isSubscribe){
                                                return(
                                                    <option selected={item.id === 2} value={item.id}>{trans[item.type]} {item.duration} {trans.month}</option>
                                                );
                                            }
                                        })}
                                    </select>
                                    <button className="btn btn-group btn-outline-success">{trans.validate}</button>
                                </form>
                            </div>

                            <div className="border-red rounded-more pad-30 text-center col-md-6 col-sm-12 marg-top-10 marg-bottom-20">
                                <h1>* {trans.options}</h1>
                                <form onChange={this.handleChange} onSubmit={this.handleShop} className="d-flex flex-row justify-content-between" id="notsub">
                                    <select>
                                        {items.map(item => {
                                            if (!item.isSubscribe){
                                                return(
                                                    <option selected={item.id === 1} value={item.id}>{item.quantity} {trans[item.type]} CHF {item.price}</option>
                                                )
                                            }
                                        })}
                                    </select>
                                    <button className="btn btn-group btn-outline-success">{trans.validate}</button>
                                </form>
                            </div>
                        </div>
                    <div className="col-12 col-md-10 offset-md-1">
                        <div className="table-responsive">
                            <table className="table table-bordered">
                                <thead className="bg-danger text-white">
                                    <tr>
                                        <td scope="col">{trans.function}</td>
                                        <td scope="col" className="text-center">{trans.registered}</td>
                                        <td scope="col" className="text-center">{trans.basic}</td>
                                        <td scope="col" className="text-center">{trans.medium}</td>
                                        <td scope="col" className="text-center">{trans.premium}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.profilCreate}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.profilConsult}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.profilSearch}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.messageReceive}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.flowerReceive}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.messageSend}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.groupJoin}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.flowerSend}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center">{trans.options} *</td>
                                        <td scope="col" className="text-center">5/{trans.month} <br/>+ {trans.options} *</td>
                                        <td scope="col" className="text-center text-success">{trans.unlimited}</td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.favoriteList}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center">{trans.options} *</td>
                                        <td scope="col" className="text-center">5/{trans.month} <br/>+ {trans.options} *</td>
                                        <td scope="col" className="text-center text-success">{trans.unlimited}</td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.groupCreate}</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">Matching</td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faTimes}  color={"rgb(0,0,0)"} /></td>
                                        <td scope="col" className="text-center"><FontAwesomeIcon icon={faCheck}  color={"rgb(0,255,0)"} /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div className="col-12 col-md-10 offset-md-1">
                        <div className="table-responsive">
                            <table className="table table-bordered">
                                <thead className="bg-danger text-white">
                                <tr>
                                    <td scope="col">{trans.subscribe}</td>
                                    <td scope="col" className="text-center">1 {trans.month}</td>
                                    <td scope="col" className="text-center">3 {trans.month}</td>
                                    <td scope="col" className="text-center">6 {trans.month}</td>
                                    <td scope="col" className="text-center">12 {trans.month}</td>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.basic}</td>
                                        <td scope="col" className="text-center"><span className="text-success">CHF 49.00</span> </td>
                                        <td scope="col" className="text-center">
                                            <div>-35%</div>
                                            <div>CHF 31.85/{trans.month}</div>
                                            <div className="text-success">{trans.is} CHF 95.55</div>
                                        </td>
                                        <td scope="col" className="text-center">
                                            <div>-51%</div>
                                            <div>CHF 24.00/{trans.month}</div>
                                            <div className="text-success">{trans.is} CHF 144.00</div>
                                        </td>
                                        <td scope="col" className="text-center">
                                            <div>-67%</div>
                                            <div>CHF 16.15/{trans.month}</div>
                                            <div className="text-success">{trans.is} CHF 193.80</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.medium}</td>
                                        <td scope="col" className="text-center"><span className="text-success">CHF 66.00</span> </td>
                                        <td scope="col" className="text-center">
                                            <div>-40%</div>
                                            <div>CHF 39.60/{trans.month}</div>
                                            <div className="text-success">{trans.is} CHF 118.80</div>
                                        </td>
                                        <td scope="col" className="text-center">
                                            <div>-55%</div>
                                            <div>CHF 29.70/{trans.month}</div>
                                            <div className="text-success">{trans.is} CHF 178.20</div>
                                        </td>
                                        <td scope="col" className="text-center">
                                            <div>-70%</div>
                                            <div>CHF 19.80/{trans.month}</div>
                                            <div className="text-success">{trans.is} CHF 237.60</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="col" className="bg-pink">{trans.premium}</td>
                                        <td scope="col" className="text-center"><span className="text-success">CHF 98.00</span> </td>
                                        <td scope="col" className="text-center">
                                            <div>-45%</div>
                                            <div>CHF 53.90/{trans.month}</div>
                                            <div className="text-success">{trans.is} CHF 161.70</div>
                                        </td>
                                        <td scope="col" className="text-center">
                                            <div>-59%</div>
                                            <div>CHF 40.18/{trans.month}</div>
                                            <div className="text-success">{trans.is} CHF 241.08</div>
                                        </td>
                                        <td scope="col" className="text-center">
                                            <div>-73%</div>
                                            <div>CHF 26.46/{trans.month}</div>
                                            <div className="text-success">{trans.is} CHF 317.50</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            );
        }
        else {
            return (<div> </div>)
        }
    }
}

ReactDOM.render(<Shop/>, document.getElementById('shop'));
