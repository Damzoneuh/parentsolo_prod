import React, {Component} from 'react';
import HeaderShowProfile from "./HeaderShowProfile";
import ProfilBody from "../modules/dashboard/ProfilBody";

export default class ProfilShow extends Component{
    constructor(props) {
        super(props);
    }


    render() {
        const {profile, trans} = this.props;
        if (Object.entries(profile).length > 0){
            return(
                <div>
                    <HeaderShowProfile profile={this.props.profile} trans={trans} />
                    <ProfilBody profile={this.props.profile} />
                </div>
            )
        }
    }


}