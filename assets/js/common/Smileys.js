import React, {Component} from 'react';

export default class Smileys extends Component{
    constructor(props) {
        super(props);
        this.state = {
            target: this.props.element,
            smileys: [
                '😀',
                '😄',
                '😁',
                '😊',
                '😍',
                '😘',
                '☹️',
                '😲',
                '😱',
                '😠',
                '😉',
                '👍'
            ],
            boxOpen: false
        };
        this.handleOpenBox = this.handleOpenBox.bind(this);
        this.appendForm = this.appendForm.bind(this);
    }

    handleOpenBox(){
        this.setState({
            boxOpen: this.state.boxOpen ? false : true
        })
    }

    appendForm(e){
        let el = document.getElementById(this.state.target);
        el.value = el.value + e.target.innerText;
    }


    render() {
        const {target, smileys, boxOpen} = this.state;
        return (
            <div className="marg-10">
                <span onClick={this.handleOpenBox} className="position-relative">😀</span>
                <div className={boxOpen ? 'smiley-box' : 'none'}>
                    {smileys ? smileys.map(smiley => {
                        return (
                            <a onClick={this.appendForm}>{smiley}</a>
                        )
                    }) : ''}
                </div>
            </div>
        );
    }


}