import React, {Component} from 'react';
import axios from 'axios';
import ImageRenderer from "../../common/ImageRenderer";
import defaultMan from '../../../fixed/HommeDefaut.png';
import defaultWoman from '../../../fixed/FemmeDefaut.png';


export default class LastProfiles extends Component{
    constructor(props) {
        super(props);
        this.state = {
            isLoaded: false,
            ids: [],
            userDetails: [],
            trans: []
        };
        this.handleProfile = this.handleProfile.bind(this);
    }

    componentDidMount(){
        let limit = 5;
        if (window.matchMedia('(max-width: 752px)').matches){
            limit = 3;
        }
        axios.get('/api/last/profile/' + limit)
            .then(res => {
                this.setState({
                    ids: res.data,
                });
                res.data.map(data => {
                   axios.get('/api/profile/light/' + data.id)
                        .then(user => {
                            this.pushUsers(user.data)
                        })
                });
            });
        axios.get('/api/trans/search')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            });
    }

    pushUsers(user){
        let previous = this.state.userDetails;
        previous.push(user);
        this.setState({
            userDetails: previous,
            isLoaded: true
        })
    }

    handleProfile(val){
        this.props.handleProfile(val);
        axios.post('/api/set/visit', {target: val})
            .then(res => {

            })
    }

    render() {
        const {isLoaded, userDetails, trans} = this.state;
        if (isLoaded && trans){
           return (
               <div className="row text-center marg-top-10 marg-bottom-10">
                   <div className="marg-10 w-100">
                       <h3 className="marg-10">{trans.lastProfileTitle.toUpperCase()}</h3>
                   </div>
                   <div className="col-sm-12 w-100">
                       <div className="d-flex flex-row justify-content-between align-items-center" >
                           {userDetails.map(user => {
                               if (user.img !== null){
                                   return (
                                       <div>
                                           <a href="#" className="last-profile-link" onClick={() => this.handleProfile(user.id)}>
                                               <div className="d-flex justify-content-center align-items-center">
                                                    <ImageRenderer alt={"profile"} className={"testimony-img-dashboard red-border position-relative"} id={user.img}/>
                                                    <div className="position-absolute bg-danger hover-img">
                                                        <div className="text-white text-profile-hover">
                                                            {user.age}<br/>
                                                            {user.canton}<br/>
                                                            {/*{user.child}*/}
                                                        </div>
                                                    </div>
                                               </div>
                                           </a>
                                           <div className="text-center">
                                               {typeof user.pseudo !=='undefined' ? user.pseudo.toUpperCase() : ''}
                                           </div>
                                       </div>
                                   )
                               }
                               if (user.isMan === 1){
                                   return (
                                       <div>
                                           <a href="#" className="last-profile-link" onClick={() => this.handleProfile(user.id)}>
                                               <div className="d-flex justify-content-center align-items-center">
                                                   <img src={defaultMan} alt={"profil"} className={"testimony-img-dashboard red-border position-relative"}/>
                                                   <div className="position-absolute bg-danger hover-img">
                                                       <div className="text-white text-profile-hover">
                                                           {user.age}<br/>
                                                           {user.canton}<br/>
                                                           {/*{user.child}*/}
                                                       </div>
                                                   </div>
                                               </div>
                                           </a>
                                           <div className="text-center">
                                               {typeof user.pseudo !=='undefined' ? user.pseudo.toUpperCase() : ''}
                                           </div>
                                       </div>
                                   )
                               }
                               else {
                                   return (
                                       <div>
                                           <a href="#" className="last-profile-link" onClick={() => this.handleProfile(user.id)}>
                                               <div className="d-flex justify-content-center align-items-center">
                                                   <img src={defaultWoman} alt={"profil"} className={"testimony-img-dashboard red-border position-relative"}/>
                                                   <div className="position-absolute bg-danger hover-img">
                                                       <div className="text-white text-profile-hover">
                                                           {user.age}<br/>
                                                           {user.canton}<br/>
                                                           {/*{user.child}*/}
                                                       </div>
                                                   </div>
                                               </div>
                                           </a>
                                           <div className="text-center">
                                               {typeof user.pseudo !=='undefined' ? user.pseudo.toUpperCase() : ''}
                                           </div>
                                       </div>
                                   )
                               }
                           })}
                       </div>
                   </div>
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