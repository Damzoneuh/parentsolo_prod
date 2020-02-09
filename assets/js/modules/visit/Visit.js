import React, {Component} from 'react';
import ReactDom from 'react-dom';
import UnderNav from "../../common/UnderNav";
import axios from 'axios';
import ImageRenderer from "../../common/ImageRenderer";
import defaultMan from "../../../fixed/HommeDefaut.png";
import defaultWoman from '../../../fixed/FemmeDefaut.png';
import ProfilShow from "../../common/ProfilShow";

export default class Visit extends Component{
    constructor(props) {
        super(props);
        this.state = {
            visits: null,
            trans: null,
            profile: null
        };
        this.handleClick = this.handleClick.bind(this);
    }

    componentDidMount(){
        axios.get('/api/visit')
            .then(res => {
                this.setState({
                    visits: res.data
                })
            });
        axios.get('/api/trans/all')
            .then(res => {
                this.setState({
                    trans: res.data
                });
            })
    }

    handleClick(val){
        axios.get('/api/profile/' + val)
            .then(res => {
                this.setState({
                    profile: res.data
                })
            })
    }

    render() {
        const {visits, trans, profile} = this.state;
        if (visits && trans && !profile){
            return (
                <div>
                    <UnderNav data={'visit'} />
                    <div className="container">
                        <div className="text-center marg-top-10"><h1>{trans['my.visits']}</h1></div>
                        {visits && visits.length === 0 ? <div className="text-center"> <h1>{trans['no visit']}</h1></div> :
                            <div className="row">
                                {visits.map(visit => {
                                    return (
                                        <div className="col" key={visit.id}>
                                            <div className="testimony-wrap marg-bottom-10 marg-top-10">
                                                <div className="d-flex align-items-center justify-content-between">
                                                    {visit.img ? <ImageRenderer id={visit.img} alt={"profile image"} className={"thumb-profile-img"}/> : visit.isMan ?
                                                        <img src={defaultMan} className="thumb-profile-img" alt="profile image" /> :
                                                        <img src={defaultWoman} className="thumb-profile-img" alt="profile image"/>
                                                    }
                                                    <div className="text-center">
                                                        <div><h1>{visit.alias.toUpperCase()}</h1></div>
                                                        <div><h2>{visit.age}  {trans['years.old']}</h2></div>
                                                        <div><h5>{visit.canton} - {visit.city}</h5></div>
                                                    </div>
                                                </div>
                                                <div className="text-center">
                                                    <p>{visit.date}</p>
                                                    <button className="btn btn-group btn-light" onClick={() => this.handleClick(visit.id)}>{trans.view}</button>
                                                </div>
                                            </div>
                                        </div>
                                    )
                                })}
                            </div>
                        }
                    </div>
                </div>
            );
        }
        else if (trans && profile){
            return (
                <div>
                    <UnderNav data={"visit"}/>
                    <ProfilShow profile={profile} trans={trans}/>
                </div>
            )
        }
        else {
            return (
                <div>

                </div>
            )
        }
    }
}
ReactDom.render(<Visit/>, document.getElementById('visit'));