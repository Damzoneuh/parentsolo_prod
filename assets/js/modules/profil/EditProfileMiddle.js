import React, {Component} from 'react';
import axios from 'axios';
import Logger from "../../common/Logger";
import HandleOneToMany from "./HandleOneToMany";

export default class EditProfileMiddle extends Component{
    constructor(props) {
        super(props);
        this.state = {
            message: null,
            type: null,
            child: []
        };
        this.handleSendChildWanted = this.handleSendChildWanted.bind(this);
    }

    componentDidMount(){
        let child = [];
        for (let i = 0; i < 4; i++){
            child.push(i);
            this.setState({
                child: child
            })
        }
    }

    handleSendChildWanted(e){
        axios.put("/api/child/wanted", {value: parseInt(e.target.value)})
            .then(res => {
                this.setState({
                    message: res.data,
                    type: 'success'
                });
                setInterval(() => this.setState({message: null, type: null}), 2000);
            })
    }


    render() {
        const {trans, user, tables} = this.props;
        const {message, type, child} = this.state;
        return (
            <div className="marg-top-20 pad-30 marg-bottom-20">
                {message && type ? <Logger message={message} type={type} /> : ''}
                <div className="marg-top-10 d-flex justify-content-around align-items-center">
                    <div className="d-flex flex-row justify-content-center">
                        <div className="flex-row d-flex">
                            <div className="sophie-bg-border"></div>
                        </div>
                    </div>
                    <div className="d-flex align-items-start ">
                        <div className="triangle-edit"></div>
                        <div className="edit-bubble d-flex flex-column align-items-center justify-content-center text-center text-danger">
                            {trans['sophie.edit.profil']}
                            {/*<br/><span className="threat-red pulse"></span>*/}
                        </div>
                    </div>
                </div>
                <div className="rounded-more bg-light marg-bottom-20">
                    <div className="rounded-more bg-light marg-top-20 marg-bottom-20 pad-30">
                        <h3 className="border-bottom-black pad-bottom-10">{trans.personality}</h3>
                            <div className="d-flex justify-content-between align-items-start marg-top-10">
                                <div>{trans["relationship.search"]} :
                                </div>
                                <HandleOneToMany user={user} tables={tables} userField={'relation'} tableName={'Relationship'} trans={trans} catField={'personality'}/>
                            </div>
                            <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans.temperament} :</div>
                                <HandleOneToMany user={user} tables={tables} userField={'temperament'} tableName={'Temperament'} trans={trans} catField={'personality'}/>
                            </div>
                            <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["childs.wanted"]} :</div>
                                <div>
                                    <form className="form" onChange={this.handleSendChildWanted}>
                                        <div className="form-group">
                                            <select>
                                                <option value={null}>{trans["I keep it to myself."]} </option>
                                                {child.length > 0 ? child.map(c => {
                                                    let defaultChecked = false;
                                                    if (user.wantedChild && parseInt(user.wantedChild) === c){
                                                        defaultChecked = true;
                                                    }
                                                    return (
                                                        <option value={c} selected={defaultChecked} key={c}>{c}</option>
                                                    )
                                                }) : ''}
                                            </select>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["nationality"]} :</div>
                                <HandleOneToMany user={user} tables={tables} userField={'nationality'} tableName={'Nationality'} trans={trans} catField={'personality'}/>
                            </div>

                    </div>
                </div>
                <div className="rounded-more bg-light marg-bottom-20">
                    <div className="rounded-more bg-light marg-top-20 marg-bottom-20 pad-30">
                        <h3 className="border-bottom-black pad-bottom-10">{trans.lifestyle}</h3>
                        <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["family.status"]} :</div>
                            <HandleOneToMany user={user} tables={tables} userField={'status'} tableName={'Status'} trans={trans} catField={'personality'}/>
                        </div>
                        <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["way.of.life"]} :</div>
                            <HandleOneToMany user={user} tables={tables} userField={'lifeStyle'} tableName={'LifeStyle'} trans={trans} catField={'lifeStyle'}/>
                        </div>
                        <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["childs.care"]} :</div>
                            <HandleOneToMany user={user} tables={tables} userField={'childGuard'} tableName={'ChildGard'} trans={trans} catField={'lifeStyle'}/>
                        </div>
                        <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["religion"]} :</div>
                            <HandleOneToMany user={user} tables={tables} userField={'religion'} tableName={'Religion'} trans={trans} catField={'lifeStyle'}/>
                        </div>
                        <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["smoke"]} :</div>
                            <HandleOneToMany user={user} tables={tables} userField={'smoke'} tableName={'Smoke'} trans={trans} catField={'lifeStyle'}/>
                        </div>
                        <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["studies.level"]} :</div>
                            <HandleOneToMany user={user} tables={tables} userField={'studies'} tableName={'Studies'} trans={trans} catField={'lifeStyle'}/>
                        </div>
                        <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["line.of.buisness"]} :</div>
                            <HandleOneToMany user={user} tables={tables} userField={'activity'} tableName={'Activity'} trans={trans} catField={'lifeStyle'}/>
                        </div>
                    </div>
                </div>
                <div className="rounded-more bg-light marg-bottom-20">
                    <div className="rounded-more bg-light marg-top-20 marg-bottom-20 pad-30">
                        <h3 className="border-bottom-black pad-bottom-10">{trans.appearence}</h3>
                        <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["eyes"]} :</div>
                            <HandleOneToMany user={user} tables={tables} userField={'eyes'} tableName={'Eyes'} trans={trans} catField={'appearance'}/>
                        </div>
                        <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["hair"]} :</div>
                            <HandleOneToMany user={user} tables={tables} userField={'hair'} tableName={'Hair'} trans={trans} catField={'appearance'}/>
                        </div>
                        <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["hair.style"]} :</div>
                            <HandleOneToMany user={user} tables={tables} userField={'hairStyle'} tableName={'HairStyle'} trans={trans} catField={'appearance'}/>
                        </div>
                        <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["silhouette"]} :</div>
                            <HandleOneToMany user={user} tables={tables} userField={'silhouette'} tableName={'Silhouette'} trans={trans} catField={'appearance'}/>
                        </div>
                        <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["size"]} :</div>
                            <HandleOneToMany user={user} tables={tables} userField={'size'} tableName={'Size'} trans={trans} catField={'appearance'}/>
                        </div>
                        <div className="d-flex justify-content-between align-items-start marg-top-10"><div>{trans["origin"]} :</div>
                            <HandleOneToMany user={user} tables={tables} userField={'origin'} tableName={'Origin'} trans={trans} catField={'appearance'}/>
                        </div>
                    </div>
                </div>
            </div>
        );
    }


}