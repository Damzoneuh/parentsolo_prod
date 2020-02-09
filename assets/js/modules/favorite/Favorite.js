import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import UnderNav from "../../common/UnderNav";
import ImageRenderer from "../../common/ImageRenderer";
import defaultMan from "../../../fixed/HommeDefaut.png";
import defaultWoman from "../../../fixed/FemmeDefaut.png";
import ProfilShow from "../../common/ProfilShow";

let el = document.getElementById('favorite');

export default class Favorite extends Component{
    constructor(props) {
        super(props);
        this.state = {
            user: null,
            trans: null,
            favorite: null,
            profile: null,
            showTrans: null
        };
        this.handleShowProfile = this.handleShowProfile.bind(this);
    }

    componentDidMount(){
        axios.get('/api/user')
            .then(res => {
                this.setState({
                    user: res.data
                })
            });
        axios.get('/api/trans/all')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            });
        axios.get('/api/user/favorite')
            .then(res => {
                this.setState({
                    favorite: res.data
                });
            });

        axios.get('/api/trans/search')
            .then(res => {
                this.setState({
                    showTrans: res.data
                })
            })
    }

    handleShowProfile(id){
        axios.get('/api/profile/' + id)
            .then(res => {
                this.setState({
                    profile: res.data
                })
            })
    }

    render() {
        const {trans, user, favorite, profile, showTrans} = this.state;
        if (user && trans && !profile){
            return (
                <div>
                    <UnderNav data={'favorite'}/>
                    <div className="container-fluid">
                        <div className="row">
                            {favorite && favorite.length > 0 ?
                                favorite.map(f => {
                                    return (
                                        <div className="col border-red rounded-more bg-light shadow marg-20">
                                            <div className="text-center marg-top-10 marg-bottom-10">
                                                {f.img ? <ImageRenderer id={f.img} alt={"profile image"} className={"thumb-profile-img rounded-more"} /> : parseInt(el.dataset.isMan) ?
                                                    <img src={defaultMan} alt="profil-img" className={"thumb-profile-img rounded-more"}/> :
                                                    <img src={defaultWoman} alt="profil-img" className={"thumb-profile-img rounded-more"}/>}
                                            </div>
                                            <div className="text-center">
                                                <h4>{f.alias.toUpperCase()}</h4>
                                            </div>
                                            <div className="marg-top-10 text-center marg-bottom-20">
                                                <button className="btn btn-group btn-outline-danger" onClick={() => this.handleShowProfile(f.id)}>{trans['view.more']}</button>
                                            </div>
                                        </div>
                                    )
                                })
                            : '' }
                        </div>
                    </div>
                </div>
            );
        }
        if (profile && showTrans){
            return (
                <div>
                    <UnderNav data={'favorite'}/>
                    <ProfilShow trans={showTrans} profile={profile} />
                </div>
            )
        }
        else {
            return (
                <div>

                </div>
            );
        }
    }
}

ReactDOM.render(<Favorite/>, document.getElementById('favorite'));