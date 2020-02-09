import React, {Component} from 'react'
import axios from 'axios';
import Cropper from 'react-cropper';

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
            selectDay: [],
            selectMonth: [],
            selectYear: [],
            day: 1,
            month: 1,
            year: 1900,
            sex: true
        };
        this.cropper = React.createRef();
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this._crop = this._crop.bind(this);
    }

    componentDidMount(){
        let day = [];
        let month = [];
        let year = [];
        let date = new Date().getFullYear();

        for (let i = 1; day.length < 31; i++){
            day.push(i);
            this.setState({
                selectDay: day
            })
        }

        for (let i = 1; month.length < 12; i++){
            month.push(i);
            this.setState({
                selectMonth: month
            })
        }

        for (let i = 1950; i <= date; i++){
            year.push(i);
            this.setState({
                selectYear: year
            })
        }

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
        this.state.img ? data.append('file', this.state.img) : data.append('file', null);
        data.append('name', this.state.name);
        data.append('day', this.state.day);
        data.append('month', this.state.month);
        data.append('year', this.state.year);
        data.append('sex', this.state.sex);
        axios.post('/api/child/add', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
            .then(res => {
                this.setState({
                    img: null,
                    blob: null,
                    name: null,
                    type: null
                });
                window.location.href='/edit/profile'
            })

    }

    getAllImg(){
        axios.get('/api/img')
            .then(res => {
                this.setState({
                    path: res.data
                })
            })
    }

    render() {
        const {trans} = this.props;
        const {selectYear, selectDay, selectMonth} = this.state;
        return (
            <div className="container">
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
                                <label htmlFor="file">{trans.image}</label>
                                <input type="file" className="form-control-file" id="file" name="file" />
                            </div>
                        </div>
                    </div>
                    <div className="form-group">
                        <label htmlFor="name">{trans.firstname} </label>
                        <input type="text" name="name" className="form-control" required />
                    </div>
                    <div className="form-group">
                        <div className="row">
                            <div className="col-12 text-center">
                                <h4>{trans['birth date']}</h4>
                            </div>
                            <div className="col-4">
                                <label htmlFor="day">{trans.day}</label>
                                <select name="day" className="form-control">
                                    {selectDay.map(day => {
                                        return (
                                            <option value={day} key={day}>{day}</option>
                                        )
                                    })}
                                </select>
                            </div>
                            <div className="col-4">
                                <label htmlFor="month">{trans.month}</label>
                                <select name="month" className="form-control">
                                    {selectMonth.map(month => {
                                        return (
                                            <option value={month} key={month}>{month}</option>
                                        )
                                    })}
                                </select>
                            </div>
                            <div className="col-4">
                                <label htmlFor="year">{trans.year}</label>
                                <select name="year" className="form-control">
                                    {selectYear.map(year => {
                                        return (
                                            <option key={year} value={year}>{year}</option>
                                        )
                                    })}
                                </select>
                            </div>
                        </div>
                        <div className="row marg-top-20">
                            <div className="col-2 offset-5" >
                                <select name="sex" className="form-control">
                                    <option value={true}>{trans.boy}</option>
                                    <option value={false}>{trans.girl}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div className="form-group">
                        <div className="row">
                            <div className="col-12 text-center">
                                <button className="btn btn-outline-danger btn-group">{trans.validate}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        );
    }

}