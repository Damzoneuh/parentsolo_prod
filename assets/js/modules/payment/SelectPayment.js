import React, {Component} from 'react';

export default class SelectPayment extends Component{
    constructor(props){
        super(props);
    }

    render() {
        return (
            <div className="row marg-0">
                <div className="col-lg-6 col-md-6 col-12">
                    <div className="testimony-wrap" onClick={() => this.props.handler(2)}>
                        <div className="card-body">
                            Credit card
                        </div>
                    </div>
                </div>
                <div className="col-lg-6 col-md-6 col-12" onClick={() => this.props.handler(3)}>
                    <div className="testimony-wrap">
                        <div className="card-body">
                            PayPal
                        </div>
                    </div>
                </div>
            </div>
        );
    }

}