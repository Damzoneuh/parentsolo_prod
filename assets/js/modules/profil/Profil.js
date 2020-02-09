import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import Image from "../../common/Image";
import Shop from "./Shop";

export default class Profil extends Component{
    constructor(props){
        super(props);
        this.state = {
            isLoaded: true,
            tab: 0
        }
    }


    render() {
        const {isLoaded, tab} = this.state;
        if (!isLoaded){
            return(
                <div className="container-loader">
                    <div className="ring">
                        <span className="ring-span"></span>
                    </div>
                </div>
            )
        }
        if (tab === 0){
            return (
                <div className="flex-row w-75">
                    <div className="col-12">
                        <Shop/>
                    </div>
                    <div className="col-12">
                        <Image/>
                    </div>
                </div>
            )
        }
    }

}

ReactDOM.render(<Profil/>, document.getElementById('profil'));