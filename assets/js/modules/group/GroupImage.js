import React, {Component} from 'react'
import axios from 'axios';
import Cropper from 'react-cropper';
import Logger from "../../common/Logger";

export default class Image extends Component{
    constructor(props){
        super(props);
        this.state = {
            isImgLoaded: false,
            img: null,
            name: null,
            description: null,
            preview: null,
            resize: null,
            path: [],
            blob: null,
            type: null,
            message: null,
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
        data.append('description', this.state.description);
        if (this.props.right){
            axios.post('/api/group/create', data, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(res => {
                this.setState({
                    img: null,
                    blob: null,
                    name: null,
                    type: null,
                    message: res.data,
                    logType: 'success'
                });
                setInterval(() => {
                    window.location.href='/group'
                }, 2000)
            })
        }
        else {
            window.location.href = '/shop'
        }

    }

    render() {
        const {trans} = this.props;
        const {logType, message} = this.state;
        return (
            <div>
                {message && logType ? <Logger message={message} type={logType} /> : ''}
                <form onChange={this.handleChange} onSubmit={this.handleSubmit} method="post">
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
                                <label htmlFor="file">{trans.img}</label>
                                <input type="file" className="form-control-file" id="file" name="file" required={true}/>
                            </div>
                        </div>
                    </div>
                    <div className="form-group">
                        <label htmlFor="name">{trans.name}</label>
                        <input type="text" name="name" className="form-control" required={true}/>
                    </div>
                    <div className="form-group">
                        <label htmlFor="description">{trans.description}</label>
                        <textarea name="description" className="form-control" required={true}/>
                    </div>
                    <div className="form-group">
                        <button className="btn btn-primary btn-group">{trans.validate}</button>
                    </div>
                </form>
            </div>
        );
    }

}