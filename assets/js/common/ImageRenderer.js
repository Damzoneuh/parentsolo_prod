import React, {Component} from 'react';

export default class ImageRenderer extends Component{
    constructor(props) {
        super(props);
    }


    render() {
        return (
            <img src={'https://parentsolo.disons-demain.be/api/img/render/' + this.props.id} alt={this.props.alt} className={this.props.className} />
        )
    }
}