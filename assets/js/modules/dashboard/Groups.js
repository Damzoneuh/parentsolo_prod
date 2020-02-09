import React, {Component} from 'react';
import axios from 'axios';
import groupImg from '../../../fixed/Group.svg';
import ImageRenderer from "../../common/ImageRenderer";

export default class Groups extends Component{
    constructor(props) {
        super(props);
        this.state = {
            isLoaded: false,
            trans: [],
            group: []
        }
    }

    componentDidMount(){
        axios.get('/api/group/trans')
            .then(res => {
                this.setState({
                    trans: res.data
                });
                axios.get('/api/last/group')
                    .then(res => {
                        this.setState({
                            group: res.data,
                            isLoaded: true
                        })
                    })
            })
    }

    render() {
        const {isLoaded, trans, group} = this.state;
        if (isLoaded){
            return (
                <div className="rounded-more border-red marg-top-10">
                    <div className="d-flex flex-row justify-content-start align-items-center ">
                        <img src={groupImg} alt={"group image"} className="search-glass"/>
                        <h3 className="marg-10">{trans.group.toUpperCase()}</h3>
                    </div>
                    <p className="font-size-20 text-center">{trans.groupDescribe}</p>
                    <div className="d-flex flex-row justify-content-around align-items-center">
                        <div className="w-25">
                            {group.img ? <ImageRenderer id={group.img} alt={"group image"} className={"group-thumb"} /> :
                            <img src={groupImg} alt={"group image"} className="group-thumb"/>}
                        </div>
                        <div className="w-75">
                            <a href={"/group/" + group.id} className="text-danger font-size-20">{trans.lastGroupLink}</a><br/>
                            {trans.createdBy} : {group.createdBy}
                        </div>
                    </div>
                    <div className="d-flex flex-row justify-content-around align-items-center">
                        <a href={"/group"} className="btn btn-outline-danger btn-group marg-top-20 marg-bottom-10">{trans.showLink}</a>
                        <a href={"/group"} className="btn btn-outline-danger btn-group marg-top-20 marg-bottom-10">{trans.createLink}</a>
                    </div>
                </div>
            );
        }
        else {
            return (
                <div>

                </div>
            );
        }
    }


}