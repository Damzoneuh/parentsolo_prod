import React, {Component} from 'react';

export default class Logger extends Component{
    constructor(props){
        super(props);
        this.state = {
            display: true
        };
        this.stopDisplay = this.stopDisplay.bind(this);
    }

    stopDisplay(){
        this.setState({
            display: false
        })
    }

    render() {
        const {display} = this.state;
        setTimeout(this.stopDisplay, 20000);
        if (display && this.props.message){
            return (
                <div className={"logger-box " + (this.props.type)}>
                    {this.props.message ? this.props.message.toUpperCase() : ''}
                </div>
            );
        }
        else {
            return (<div className="none"></div>)
        }
    }
}