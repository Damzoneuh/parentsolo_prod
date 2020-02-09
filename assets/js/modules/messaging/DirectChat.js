import React, {Component} from 'react';
//import WS from '../../../../vendor/gos/web-socket-bundle/Resources/public/js/gos_web_socket_client';

export default class DirectChat extends Component{
    constructor(props) {
        super(props);
        this.state = {
            target: this.props.target,
            message: null,
            formText: null,
            content: this.props.content,
            isDeploy: false
        };
        this.handleChangeMessage = this.handleChangeMessage.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleDeploy = this.handleDeploy.bind(this);
    }

    handleChangeMessage(e){
        e.preventDefault();
        let message = {
            message: this.state.formText,
            action: 'send',
            target: this.props.target,
            from: this.props.user
        };
        this.props.submitMessage(message);
        document.getElementById('message').value = null;
    }

    handleChange(e){
        this.setState({
            formText: e.target.value
        })
    }

    handleDeploy(){
        if (!this.state.isDeploy) {
            this.setState({
                isDeploy: true
            });
            this.markAsRead();
        }
        else {
            this.setState({
                isDeploy: false
            })
        }
    }

    markAsRead(){
        let data = {
            target: this.props.target,
            action: 'read'
        };
        this.props.markAsRead(data);
    }

    componentDidUpdate(prevProps){
        if (prevProps.content !== this.state.content){
            this.setState({
                content: this.props.content
            })
        }
    }

    render() {
        const {content, isDeploy} = this.state;
        if (content.length > 0){
            console.log(content);
            return (
                isDeploy ?
                    <div className="wrap">
                        <div className={"chat-top rounded-top position-absolute"} onClick={this.handleDeploy}>{this.props.target}</div>
                        <form onSubmit={this.handleChangeMessage} className="rounded-top">
                            <div className={"chat chat-box position-absolute w-100"}>
                                {content[1].message.map((c, key) => {
                                    return(
                                        <p key={key}>{c.message} {c.isRead ? 'v' : ''}</p>
                                    )
                                })}
                            </div>
                            <input className="form-text" onChange ={this.handleChange} name="message" id="message"/>
                            <button className="display-none">Send</button>
                        </form>
                    </div>
                    :
                    <div className="minimize" onClick={this.handleDeploy}>
                        {this.props.target}
                    </div>
            );
        }
        else {
            return (
                <div></div>
            )
        }
    }

}