import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import UnderNav from "../../common/UnderNav";
import Pagination from "../../common/Pagination";
import Logger from "../../common/Logger";
import DefaultMan from '../../../fixed/HommeDefaut.png';
import DefaultWoman from '../../../fixed/FemmeDefaut.png';
import ImageRenderer from "../../common/ImageRenderer";
import TestimonyModal from "./TestimonyModal";


const el = document.getElementById('testimony');

export default class Testimony extends Component{
    constructor(props) {
        super(props);
        this.state = {
            groups: null,
            trans: null,
            rights: null,
            display: null,
            message: null,
            type: null
        };
        this.handleDisplayed = this.handleDisplayed.bind(this);
        this.joinGroup = this.joinGroup.bind(this);
        this.leaveGroup = this.leaveGroup.bind(this);
        this.getGroup = this.getGroup.bind(this);
    }

    componentDidMount(){
        this.getGroup();
        axios.get('/api/trans/all')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            });
        axios.get('/api/user/' + el.dataset.user)
            .then(res => {
                this.setState({
                    rights: res.data.isPremium
                })
            })
    }

    getGroup(){
        axios.get('/api/' + el.dataset.path)
            .then(res => {
                this.setState({
                    groups: res.data
                })
            });
    }

    handleDisplayed(data){
        this.setState({
            display: data
        });
    }

    leaveGroup(val){
        axios.put('/api/group/leave', {'group' : val})
            .then(res => {
                this.setState({
                    message: res.data,
                    type: 'success'
                });
                this.getGroup();
                setInterval(() => {
                    this.setState({
                        message: null,
                        type: null
                    });
                }, 2000)
            })
    }

    joinGroup(val){
        axios.put('/api/group/join', {'group' : val})
            .then(res => {
                this.setState({
                    message: res.data,
                    type: 'success'
                });
                this.getGroup();
                setInterval(() => {
                    this.setState({
                        message: null,
                        type: null
                    });
                }, 2000)
            })
    }


    render() {
        const {groups, trans, rights, display, message, type} = this.state;
        if (groups && trans){
            return (
                <div>
                    <UnderNav data={'testimony'}/>
                    {message ? <Logger type={type} message={message} /> : ''}
                    <div className="modal fade bd-crop-modal-lg" tabIndex="-1" role="dialog"
                         aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div className="modal-dialog modal-lg">
                            <div className="modal-content">
                                <div className="container">
                                    <div className="modal-header">
                                        <h5>{trans['let.testimony']}</h5>
                                        <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <TestimonyModal trans={trans} />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="container-fluid">
                        <div className="row">
                            <div className="col-12 text-center marg-top-10 marg-bottom-10">
                                <button className="btn btn-group btn-danger" data-toggle="modal" data-target=".bd-crop-modal-lg">{trans['let.testimony']}</button>
                            </div>
                        </div>
                        {groups && groups.length > 0 ?
                            <div className="row align-items-stretch">
                                {display ? display.map(d => {
                                        return (
                                            <div className="col text-center marg-top-10 marg-bottom-10">
                                                <div className="pad-10 diary-wrap marg-0 h-100">
                                                    <div className="d-flex justify-content-between align-items-center">
                                                        <div>{d.img ? <ImageRenderer id={d.img} className={"thumb-profile-img"} alt={"profile image"}/> :
                                                            d.isMan ? <img src={DefaultMan} alt={"group image"} className={"thumb-profile-img"}/> : <img src={DefaultWoman} alt="image profil" className={"thumb-profile-img"}/>}</div>
                                                        <div className="marg-top-10 "><h4>{d.alias.toUpperCase()}</h4><h6>{d.title.toUpperCase()}</h6></div>
                                                    </div>
                                                    <div className="marg-top-10">{d.text}</div>
                                                    {/*{g.isSub ? <button onClick={() => this.leaveGroup(g.id)} className="btn btn-group btn-outline-danger">{trans['leave.group']}</button> :*/}
                                                    {/*    <button onClick={() => this.joinGroup(g.id)} className="btn btn-group btn-outline-danger">{trans['join.group']}</button> }*/}
                                                </div>
                                            </div>
                                        )
                                    })
                                    :
                                    groups.map(g => {
                                        return (
                                            <div className="col text-center marg-top-10 marg-bottom-10">
                                                <div className="pad-10 diary-wrap marg-0 h-100">
                                                    <div className="d-flex justify-content-between align-items-center">
                                                        <div>{g.img ? <ImageRenderer id={g.img} className={"thumb-profile-img"} alt={"profile image"}/> :
                                                            parseInt(g.isMan === 1) ? <img src={DefaultMan} alt={"image profil"} className={"thumb-profile-img"}/> : <img src={DefaultWoman} alt="image profil"  className={"thumb-profile-img"}/>}</div>
                                                        <div className="marg-top-10 "><h4>{g.alias.toUpperCase()}</h4><h6>{g.title.toUpperCase()}</h6></div>
                                                    </div>
                                                    <div className="marg-top-10">{g.text}</div>
                                                    {/*{g.isSub ? <button onClick={() => this.leaveGroup(g.id)} className="btn btn-group btn-outline-danger">{trans['leave.group']}</button> :*/}
                                                    {/*    <button onClick={() => this.joinGroup(g.id)} className="btn btn-group btn-outline-danger">{trans['join.group']}</button> }*/}
                                                </div>
                                            </div>
                                        )
                                    })
                                }
                            </div>
                            : ''}
                        <div className="row">
                            <div className="col text-center">
                                {groups.length > 20 ? <Pagination data={groups} handleDisplay={this.handleDisplayed} itemsPerPage={20} trans={trans}/> : '' }
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

ReactDOM.render(<Testimony />, document.getElementById('testimony'));