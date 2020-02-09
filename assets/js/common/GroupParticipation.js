import React, {Component} from 'react';
import groupLogo from '../../fixed/Group.svg';
import axios from 'axios';


export default class GroupParticipation extends Component{
    constructor(props) {
        super(props);
        this.state = {
            groups: []
        }
    }

    componentDidMount(){
        axios.get('/api/get/group/user')
            .then(res => {
                this.setState({
                    groups: res.data
                });
            })
    }


    render() {
        const {trans} = this.props;
        const {groups} = this.state;
        return (
            <div className="rounded-more bg-light marg-top-20 pad-10">
                <div className="d-flex flex-row align-items-center justify-content-center">
                    <img src={groupLogo} alt="group logo" className="width-icon"/>
                    <h3>{trans.groups}</h3>
                </div>
                <div className="marg-20">
                    {trans["group.participation"]}
                </div>
                <div className="marg-20">
                    <ul className="font-weight-bold ">
                        {groups.length > 0 ? groups.map(group => {
                            return(
                                <li key={group.id} >{group.name}</li>
                            )
                        }) : ''}
                    </ul>
                </div>
                <div className="marg-20 text-center">
                    <a className="btn btn-group btn-outline-danger" href={"/group"} >{trans.discover}</a>
                </div>
            </div>
        );
    }


}
