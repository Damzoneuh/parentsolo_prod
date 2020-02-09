import React, {Component} from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {library} from '@fortawesome/fontawesome-svg-core';
import { faSortUp, faSortDown } from "@fortawesome/free-solid-svg-icons";
library.add(faSortDown, faSortUp);

export default class DropDownProfile extends Component{
    constructor(props) {
        super(props);
        this.state = {
            toggled: false
        };
        this.toggleDropDown = this.toggleDropDown.bind(this);
    }

    toggleDropDown(){
        if (this.state.toggled){
            this.setState({
                toggled: false
            })
        }
        else {
            this.setState({
                toggled: true
            })
        }
    }


    render() {
        const {title, content, img, imgClass} = this.props;
        const {toggled} = this.state;
        return (
            <div className="text-center marg-top-10">
                <div className="d-flex flex-row justify-content-between align-items-center">
                    <div className="d-flex align-items-end">
                        {img ? <img src={img} className={imgClass} alt={title} /> : '' }
                        <h4 className="marg-0 marg-left-10">{title}</h4>
                    </div>
                    <div onClick={this.toggleDropDown} >
                        {toggled ? <FontAwesomeIcon icon="sort-up" color={"rgb(0, 0, 0)"} className={"pad-right-10 font-size-30"}/> : <FontAwesomeIcon icon="sort-down" color={"rgb(0, 0, 0)"} className={"pad-right-10 font-size-30"}/>}
                    </div>
                </div>
                <div className={toggled ? '' : 'none'} >
                    <ul>
                        {content ? content.map(c => {
                            return(
                                <li key={c.id}>{c.name}</li>
                            )
                        }) : ''}
                    </ul>
                </div>

            </div>
        );
    }


}