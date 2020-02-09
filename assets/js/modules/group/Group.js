import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import UnderNav from "../../common/UnderNav";
import defaultGroup from '../../../fixed/Group.svg';
import GroupImage from "./GroupImage";
import ImageRenderer from "../../common/ImageRenderer";
import Pagination from "../../common/Pagination";
import Logger from "../../common/Logger";


const el = document.getElementById('group');

export default class Group extends Component{
    constructor(props) {
        super(props);
        this.state = {
            groups: null,
            trans: null,
            rights: null,
            display: null,
            message: null,
            type: null,
            viewMore: null
        };
        this.handleDisplayed = this.handleDisplayed.bind(this);
        this.joinGroup = this.joinGroup.bind(this);
        this.leaveGroup = this.leaveGroup.bind(this);
        this.getGroup = this.getGroup.bind(this);
        this.handleViewMore = this.handleViewMore.bind(this);
        this.handleCloseViewMore = this.handleCloseViewMore.bind(this);
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

    handleViewMore(id){
       this.setState({
           viewMore: id
       })
    }

    handleCloseViewMore(){
        this.setState({
            viewMore: null
        })
    }


    render() {
        const {groups, trans, rights, display, message, type, viewMore} = this.state;
        if (groups && trans){
            return (
                <div>
                    <UnderNav data={'group'}/>
                    {message ? <Logger type={type} message={message} /> : ''}
                    <div className="modal fade bd-crop-modal-lg" tabIndex="-1" role="dialog"
                         aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div className="modal-dialog modal-lg">
                            <div className="modal-content">
                                <div className="container">
                                    <div className="modal-header">
                                        <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <GroupImage trans={trans} right={rights}/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="container-fluid">
                        <div className="row">
                            <div className="col-12 text-center marg-top-10 marg-bottom-10">
                                <button className="btn btn-group btn-danger" data-toggle="modal" data-target=".bd-crop-modal-lg">{trans['groups.create.link']}</button>
                            </div>
                        </div>
                            {groups && groups.length > 0 ?
                                <div className="row align-items-stretch">
                                    {display ? display.map(d => {
                                        return (
                                            <div className="col-4 text-center marg-top-10 marg-bottom-10">
                                                <div className="rounded-more border-red h-100 pad-10">
                                                    <div>{d.img ? <ImageRenderer id={d.img} className={"thumb-profile-img"} alt={"group image"}/> : <img src={defaultGroup} alt={"group image"}/>}</div>
                                                    <div className="marg-top-10 d-flex flex-row justify-content-around"><h4>{d.name.toUpperCase()}</h4><div>{d.members} {trans.members}</div></div>
                                                    {d.isSub ? <button onClick={() => this.leaveGroup(d.id)} className="btn btn-group btn-outline-danger">{trans['leave.group']}</button> :
                                                    <button onClick={() => this.joinGroup(d.id)} className="btn btn-group btn-outline-danger">{trans['join.group']}</button> }
                                                    {viewMore && viewMore === d.id ? <button onClick={this.handleCloseViewMore} className="btn btn-group btn-outline-danger marg-left-10">{trans['hide']}</button>
                                                    : <button onClick={() => this.handleViewMore(d.id)} className="btn btn-group btn-outline-danger marg-left-10">{trans['read.more']}</button>}
                                                    <div className={viewMore && viewMore === d.id ? "marg-top-10" : 'none'}>{d.description}</div>
                                                </div>
                                            </div>
                                        )
                                    })
                                        :
                                        groups.map(g => {
                                            return (
                                                <div className="col-4 text-center marg-top-10 marg-bottom-10">
                                                    <div className="rounded-more border-red h-100 pad-10">
                                                        <div>{g.img ? <ImageRenderer id={g.img} className={"thumb-profile-img"} alt={"group image"}/> : <img src={defaultGroup} alt={"group image"}/>}</div>
                                                        <div className="marg-top-10 d-flex flex-row justify-content-around"><h4>{g.name.toUpperCase()}</h4><div>{g.members} {trans.members}</div></div>
                                                        {g.isSub ? <button onClick={() => this.leaveGroup(g.id)} className="btn btn-group btn-outline-danger">{trans['leave.group']}</button> :
                                                            <button onClick={() => this.joinGroup(g.id)} className="btn btn-group btn-outline-danger">{trans['join.group']}</button> }
                                                        {viewMore && viewMore === g.id ? <button onClick={this.handleCloseViewMore} className="btn btn-group btn-outline-danger marg-left-10">{trans['hide']}</button>
                                                            : <button onClick={() => this.handleViewMore(g.id)} className="btn btn-group btn-outline-danger marg-left-10">{trans['read.more']}</button>}
                                                        <div className={viewMore && viewMore === g.id ? "marg-top-10" : 'none'}>{g.description}</div>
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

ReactDOM.render(<Group />, document.getElementById('group'));