import React, {Component} from 'react';

export default class Paypal extends Component{
    constructor(props){
        super(props);
        window.location.href = '/paypal/' + this.props.item.id
    }
}