import React, {Component} from 'react';
import ImageCrop from "../../common/ImageCrop";
import Camera from '../../../fixed/Camera-01.svg';
import axios from 'axios';
import ImageRenderer from "../../common/ImageRenderer";
import defaultMan from '../../../fixed/HommeDefaut.png';
import defaultWoman from '../../../fixed/FemmeDefaut.png';
import Logger from "../../common/Logger";
import defaultBoy from '../../../fixed/GarconDefaut.png';
import defaultGirl from '../../../fixed/FilleDefaut.png';
import ChildModal from "../../common/ChildModal";
import ChildImageForm from "./ChildImageForm";
import ImgModal from "../../common/ImgModal";
const el = document.getElementById('edit-profile');

export default class HeaderEditProfile extends Component{
    constructor(props) {
        super(props);
        this.state = {
            img: null,
            text: null,
            message: null
        };
        this.getAllImages = this.getAllImages.bind(this);
        this.handleChange = this.handleChange.bind(this);
    }

    componentDidMount(){
        this.getAllImages();
    }

    getAllImages(){
        axios.get('/api/img')
            .then(res => {
                this.setState({
                    img: res.data
                })
            });
    }

    handleChange(e){
        this.setState({
            [e.target.name]: e.target.value
        });
    }

    handleSubmit(e){
        e.preventDefault();
        axios.post('/api/desc/set', {text: this.state.text})
            .then(res => {
                this.setState({
                    text: null,
                    message: res.data.data
                })
            })
    }

    render() {
        const {trans, user} = this.props;
        const {img, message, text} = this.state;
        let iterator = 0;
        return (
            <div className="banner-search d-flex flex-row justify-content-around align-items-stretch">
                <ImageCrop />
                <div className="modal fade bd-ad-child-modal-lg" tabIndex="-1" role="dialog"
                     aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div className="modal-dialog modal-lg">
                        <div className="modal-content">
                            <div className="modal-header">
                                <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <ChildImageForm trans={trans}/>
                        </div>
                    </div>
                </div>
                {message ? <Logger message={message} type={"success"}/> : '' }
                <div className="container-fluid">
                    <div className="row align-items-stretch">
                        <div className="col-lg-4 col-12">
                            <div className="d-flex flex-row justify-content-between align-items-stretch h-100">
                                <div className="marg-10 w-75 d-flex flex-column align-items-center justify-content-center head-camera">
                                    <img src={Camera} alt={"camera"} />
                                    <button className="btn btn-group btn-outline-danger" data-toggle="modal" data-target=".bd-crop-modal-lg">{trans["add.img"]}</button>
                                </div>
                                <div className="d-flex w-25 flex-column justify-content-between align-content-start marg-10">
                                    {img && img.length > 0 ?
                                        img.map(i => {
                                            if (i.isProfile){
                                                return (
                                                    <ImageRenderer alt={i.title} className={"header-profile-img w-100 marg-0 "} id={i.id} />
                                                )
                                            }
                                        }) : user.isMan ? <img src={defaultMan} alt={"profile image"} className={"header-profile-img w-100 marg-0 "} /> :
                                            <img src={defaultWoman} alt={"profile image"} className={"header-profile-img w-100 marg-0 "} />
                                    }
                                    {img && img.length > 1 ? img.map(i => {
                                        if (!i.isProfile && iterator === 0){
                                            iterator ++;
                                            return (
                                                <div className="position-relative">
                                                    <ImgModal img={user.img} dataTarget={"bd-profile-img-modal-lg"} />
                                                    <ImageRenderer alt={i.title} className={"header-profile-img w-100 marg-0 "} id={i.id} />
                                                    <div className="position-absolute gallery d-flex justify-content-center align-items-center" data-toggle="modal" data-target=".bd-profile-img-modal-lg">
                                                        <h1 className="font-weight-bolder">+</h1>
                                                    </div>
                                                </div>
                                            )
                                        }
                                    }) : ''}
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-5 col-12">
                            <div className="d-flex justify-content-center align-items-start flex-column pad-10">
                                <h2>{user.pseudo.toUpperCase()}</h2>
                                <h4>{user.age} | {user.city} - {user.canton}</h4>
                                <form className="bg-light rounded pad-10" onChange={this.handleChange} onSubmit={(e) => this.handleSubmit(e)}>
                                    <div className="form-group">
                                        <textarea placeholder={user.description ? user.description : trans['texte.desc']} className="form-control font-size-minor" rows="3" name="text" value={text}></textarea>
                                    </div>
                                    <div className="d-flex justify-content-between align-items-center">
                                        <div className="w-75 font-size-minor">{trans['text.validation.desc']}</div>
                                        <div className="w-25 text-right"><button className="btn btn-group btn-outline-success">{trans.validate}</button> </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div className="col-lg-3 col-12 text-center">
                            <div className="d-flex flex-column align-items-center justify-content-end h-100 pad-10">
                                <h2 className="marg-10">{user.child && user.child.length > 0 ? user.child.length : 0} {trans.child}</h2>
                                <div className="row flex-row justify-content-between align-items-stretch">
                                    <div className="col-6 align-items-stretch d-flex">
                                        <div className=" d-flex flex-row justify-content-between align-items-stretch head-camera position-relative">
                                                <div className="d-flex flex-column justify-content-center align-items-center marg-10">
                                                    <img src={Camera} alt={"camera"} data-toggle="modal" data-target=".bd-ad-child-modal-lg" className={"w-100"}/>
                                                </div>
                                                <div className="position-absolute hover-child d-flex align-items-end justify-content-center add-child" data-toggle="modal"
                                                     data-target=".bd-ad-child-modal-lg">
                                                    <button className="btn btn-group btn-outline-danger marg-bottom-10">{trans.adding}</button>
                                                </div>
                                        </div>
                                    </div>
                                    {user.child && user.child.length > 0 ?
                                        <div className="col-6 align-items-stretch d-flex">
                                            <div className=" d-flex flex-row justify-content-between align-items-stretch head-camera position-relative pad-10">
                                                {user.child && user.child[0].img && user.child[0].img.length > 0 ? <ImageRenderer alt={"child"} className={"w-100"} id={user.child[0].img[0].id}/> :
                                                    <div className="d-flex flex-column justify-content-center align-items-center w-75">
                                                        <img src={user.child[0].sex ? defaultBoy : defaultGirl} alt={"child image"} />
                                                    </div>
                                                }
                                                <div className="position-absolute hover-child d-flex align-items-center justify-content-center"
                                                     data-toggle="modal" data-target=".bd-child-modal-lg"><h1 className="text-white">+</h1></div>
                                                <ChildModal child={user.child} dataTarget={"bd-child-modal-lg"} trans={trans}/>
                                            </div>
                                        </div>
                                        : ''
                                    }
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        );
    }
}