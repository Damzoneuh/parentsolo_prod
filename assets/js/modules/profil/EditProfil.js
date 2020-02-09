import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import HeaderEditProfile from "./HeaderEditProfile";
import UnderNav from "../../common/UnderNav";
import GroupParticipation from "../../common/GroupParticipation";
import PointOfInterestEdit from "./PointOfInterestEdit";
import EditProfileMiddle from "./EditProfileMiddle";
import Adsense from "../../common/Adsense";

const el = document.getElementById('edit-profile');

export default class EditProfil extends Component{
    constructor(props) {
        super(props);
        this.state = {
            trans: null,
            user: null,
            table: [],
            diary: null
        }
    }

    componentDidMount(){
        axios.get('/api/trans/all')
            .then(res => {
                this.setState({
                    trans: res.data
                });
            });
        axios.get('/api/profile/' + el.dataset.user)
            .then(res => {
                this.setState({
                    user: res.data
                })
            });
        axios.get('/api/profile/tables/all')
            .then(res => {
                this.setState({
                    table: res.data
                })
            });
        axios.get('/api/diary')
            .then(res => {
                this.setState({
                    diary: res.data
                })
            })
    }


    render() {
        const {trans, user, table, diary} = this.state;
        if (trans && user && diary){
            return (
                <div>
                    <UnderNav data={"edit-profile"}/>
                    <HeaderEditProfile trans={trans} user={user}/>
                    <div className="container-fluid">
                        <div className="row">
                            <div className="col-lg-3 col-12">
                                <GroupParticipation profile={el.dataset.user} trans={trans} />
                                <PointOfInterestEdit trans={trans} user={user} table={table} />
                            </div>
                            <div className="col-lg-6 col-12">
                                <EditProfileMiddle trans={trans} user={user} tables={table}/>
                            </div>
                            <div className="col-lg-3 col-12">
                                <Adsense />
                                <div className="diary-wrap">
                                    <div className="flex flex-row justify-content-between marg-10">
                                        <h3>{diary.diary.toUpperCase()}</h3>
                                        {/*<img src={diaryImg} alt="diary" className="diary-img" />*/}
                                    </div>
                                    {trans["discover.text"]}
                                    <div className="text-center">
                                        <a href="#" className="btn btn-group btn-outline-light marg-top-10">{trans.discover}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            );
        }
        else {
            return (
                <div>

                </div>
            )
        }
    }
}

ReactDOM.render(<EditProfil/>, document.getElementById('edit-profile'));