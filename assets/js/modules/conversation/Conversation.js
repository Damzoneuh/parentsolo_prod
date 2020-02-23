import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import defaultMan from '../../../fixed/HommeDefaut.png';
import defaultWoman from '../../../fixed/FemmeDefaut.png';
import ImageRenderer from "../../common/ImageRenderer";
import UnderNav from "../../common/UnderNav";
import MessageModal from "../../common/MessageModal";

const el = document.getElementById('conversation');
const messageEs = new WebSocket('ws://ws.disons-demain.be:5000/c/' + el.dataset.user);

export default class Conversation extends Component{
    constructor(props) {
        super(props);
        this.state = {
            messages: null,
            trans: null,
            profiles: [],
            show: null,
            selected: null,
            messagesModal: false
        };
        this.handleShow = this.handleShow.bind(this);
        this.handleHide = this.handleHide.bind(this);
        this.handleShowMessageModal = this.handleShowMessageModal.bind(this);
        this.handleClose = this.handleClose.bind(this);
        this.handleSend = this.handleSend.bind(this);
    }

    componentDidMount(){
        messageEs.onopen = () => {};

        messageEs.onclose = () => {
            messageEs.onopen()
        };

        messageEs.onerror = () => {
            messageEs.onclose()
        };

        messageEs.onmessage = message => {
            this.setState({
                messages: JSON.parse(message.data)
            })
        };
        axios.get('/api/conversation')
            .then(res => {
                this.setState({
                    messages: Object.entries(res.data)
                });
                Object.entries(res.data).map(user => {
                   axios.get('/api/profile/' + user[0])
                       .then(res => {
                           let profiles = this.state.profiles;
                           profiles[res.data.id] = res.data;
                           this.setState({
                               profiles: profiles
                           });
                       })
                });
            });
        axios.get('/api/trans/all')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            })
    }

    handleSend(value){
        axios.get('/api/user/' + el.dataset.user)
            .then(res => {
                if (res.data.isSub){
                    messageEs.send(JSON.stringify({
                        action: 'send',
                        target: this.state.selected,
                        message: value.message
                    }));
                    setTimeout(() => window.location.href = '/conversations', 2000)
                }
                else {
                    window.location.href = '/shop';
                }
            });
        this.setState({
            messagesModal: false
        });
    }

    handleClose(){
        this.setState({
            messagesModal: false,
            selected: null
        })
    }

    handleShowMessageModal(val){
        this.setState({
            selected: val,
            messagesModal: true
        })
    }

    handleShow(val){
        this.setState({
            show: val
        })
    }

    handleHide(){
        this.setState({
            show: null
        })
    }

    render() {
        const {messages, trans, profiles, show, messagesModal} = this.state;
        if (messages && trans && profiles){
            return (
                <div>
                    <UnderNav data={'conversation'}/>
                    {messagesModal ? <MessageModal handleClose={this.handleClose} handleSend={this.handleSend} validate={trans.validate} /> : ''}
                    <div className="container">
                        <div className="row align-items-stretch ">
                            {messages.map(message => {
                                let messageIndex = message[0];
                                if (typeof profiles[messageIndex] !== 'undefined'){
                                    return (
                                        <div className="col marg-top-10 marg-bottom-20">
                                            <div className="border-red pad-30 rounded-more h-100">
                                                <div className="d-flex justify-content-between align-items-center">
                                                    {profiles[messageIndex].img ? profiles[messageIndex].img.map(i => {
                                                        if (i.isProfile){
                                                            return (
                                                                <ImageRenderer id={i.img} alt={"profile image"} className={"thumb-profile-img"}/>
                                                            )
                                                        }
                                                    }) : profiles[messageIndex].isMan ? <img src={defaultMan} alt="profile image" className="thumb-profile-img"/> :
                                                        <img src={defaultWoman} alt="profile image" className="thumb-profile-img" />
                                                    }
                                                     <div>
                                                         <div className="text-center"><h2>{profiles[messageIndex].pseudo.toUpperCase()}</h2></div>
                                                         <div className="text-center"><h2>{profiles[messageIndex].age}</h2></div>
                                                         <div className="text-center"><button className="btn btn-group btn-danger" onClick={() => this.handleShowMessageModal(profiles[messageIndex].id)}>{trans.contact}</button></div>
                                                         {show && show === message[0] ?
                                                             <div className="text-center marg-top-10"><button className="btn btn-group btn-danger" onClick={this.handleHide}>{trans.hide}</button> </div>
                                                         :
                                                             <div className="text-center marg-top-10"><button className="btn btn-group btn-danger" onClick={() => this.handleShow(message[0])}>{trans['view.more']}</button> </div>
                                                             }
                                                     </div>
                                                </div>
                                                <div className={show && show === message[0] ? "bg-light marg-top-10 pad-10" : "none"}>
                                                    {message[1].map(m => {
                                                        if (m.from === parseInt(el.dataset.user)){
                                                            return(
                                                                <div className="d-flex justify-content-end align-items-center">
                                                                    <div className="w-75 pink-rounded">
                                                                        {decodeURI(m.content)}
                                                                    </div>
                                                                </div>
                                                            )
                                                        }
                                                        else {
                                                            return (
                                                                <div className="d-flex justify-content-start align-items-center">
                                                                    <div className="red-rounded w-75">
                                                                        {decodeURI(m.content)}
                                                                    </div>
                                                                </div>
                                                            )
                                                        }
                                                    })}
                                                </div>
                                            </div>
                                        </div>
                                    )
                                }
                            })}
                        </div>
                    </div>
                </div>
            );
        }
        if(messages && trans && messages.length === 0) {
            return (
                <div>
                    <UnderNav data={'flower'}/>
                    <div className="container">
                        <div className="row">
                            <div className="col text-center marg-bottom-10 marg-top-20">
                                <h1>{trans['no message']}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            )
        }
        else {
            return (
                <div> </div>
            )
        }
    }
}

ReactDOM.render(<Conversation/>, document.getElementById('conversation'));