import React, {Component} from 'react'
import axios from 'axios';
import Cropper from 'react-cropper';
import Logger from "./Logger";

export default class Image extends Component{
    constructor(props){
        super(props);
        this.state = {
            isImgLoaded: false,
            img: null,
            name: null,
            preview: null,
            resize: null,
            path: [],
            blob: null,
            type: null,
            isProfile: false,
            logText: null,
            logType: null
        };
        this.cropper = React.createRef();
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this._crop = this._crop.bind(this);
    }

    _crop(){
       this.cropper.current.getCroppedCanvas().toBlob(blob => {
           //console.log(blob);
           this.setState({
               img: blob
           })
        });
    }

    async handleChange(e){

        if (e.target.type === "file"){
            this.setState({
                img: e.target.files[0],
                type: e.target.files[0].type,
                name: e.target.files[0].name
            });
            let resize = await this.setPreviewState(e.target.files[0]);
            let fr = new FileReader();
        }
        else {
            this.setState({
                [e.target.name]: e.target.value
            })
        }
        axios.get(this.state.preview)
            .then(res => {
               // console.log('update');
            });
    }

     setPreviewState(file) {
        return new Promise(resolve => {
            this.setState({
                preview: URL.createObjectURL(file)
            });
            resolve('done')
        });
    }

    handleSubmit(e){
        e.preventDefault();
        let data = new FormData();
        data.append('file', this.state.img);
        data.append('name', this.state.name);
        data.append('is_profile', this.state.isProfile);
        axios.post('/api/image', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
            .then(res => {
                if (res.data && res.data.success){
                    this.setState({
                        logText: res.data.success,
                        logType: 'success'
                    });
                    setTimeout(() => {
                        window.location.href='/edit/profile'
                    }, 2000)
                }
                else {
                    this.setState({
                        logText: res.data.error,
                        logType: 'error'
                    })
                }
               // window.location.href='/edit/profile'
            })

    }



    render() {
        const{logText, logType} = this.state;
        const {trans} = this.props;
        if (trans){
            return (
                <div>
                    {logText ? <Logger type={logType} message={logText} /> : ''}
                    <form onChange={this.handleChange} onSubmit={this.handleSubmit} >
                        <div className="w-75 d-flex flex-column justify-content-center align-items-center m-auto pad-30">
                            <div className="w-75">
                                {this.state.preview ? <Cropper
                                    src={this.state.preview}
                                    ref={this.cropper}
                                    style={{height: 250, width: '100%'}}
                                    crop={this._crop}
                                    rotatable={true}
                                /> : ''}

                            </div>
                        </div>

                        <div className="form-group">
                            <div className="row">
                                <div className="col-6">
                                    <label htmlFor="file">{trans['image']}</label>
                                    <input type="file" className="form-control-file" id="file" name="file" />
                                </div>
                            </div>
                        </div>
                        <div className="form-group ">
                            <label htmlFor="isProfile" className="form-check-label">{trans['is.profile']}</label>
                            <input id="isProfile" type="checkbox" name="isProfile" className="form-check-input" defaultChecked={false}/>
                        </div>
                        <div className="form-group">
                            <button className="btn btn-primary btn-group">{trans.validate}</button>
                        </div>
                    </form>
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