import React, {Component} from 'react';
import AdSense from "react-adsense-ad";


export default class Adsense extends Component{
    constructor(props) {
        super(props);

    }


    render() {
        return (
            <div className="border-bottom-red">
                <AdSense.Google client='ca-pub-1353633567358471'
                                slot='1815823308'
                                style={{ display: 'block' }}
                                format='auto'
                                height='250'
                                width='300'
                                responsive='true' />
            </div>
        );
    }
}