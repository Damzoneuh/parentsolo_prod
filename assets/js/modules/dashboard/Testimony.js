import React, {Component} from 'react';
import axios from 'axios';
import ImageRenderer from "../../common/ImageRenderer";
import defaultMan from '../../../fixed/HommeDefaut.png';
import defaultWoman from '../../../fixed/FemmeDefaut.png';

export default class Testimony extends Component{
    constructor(props) {
        super(props);
        this.state = {
            isLoaded: false,
            data: []
        }
    }

    componentDidMount(){
        axios.get('/api/testimony')
            .then(res => {
                this.setState({
                    isLoaded: true,
                    data: res.data
                })
            })
    }

    render() {
        const {isLoaded, data} = this.state;
        if (isLoaded && typeof data.id !== 'undefined'){
            return (
                <div className="border-bottom-red marg-top-20">
                    <h3>{data.testimony.toUpperCase()}</h3>
                    <div className="d-flex flex-row justify-content-between align-items-center">
                        {data.img ? <ImageRenderer id={data.img} alt={"profile image"} className={"testimony-img border-grey"}/> : ''}
                        {!data.img && data.isMan ? <img src={defaultMan} alt={"profile image"} className={"testimony-img border-grey"}/> : ''}
                        {!data.img && !data.isMan ?  <img src={defaultWoman} alt={"profile image"} className={"testimony-img border-grey"}/> : ''}
                        <div className="w-75">
                            <h4 className="text-grey">{data.pseudo.toUpperCase()}</h4>
                            <h4>{data.title}</h4>
                        </div>
                    </div>
                    <div className="hidden-text">{data.text}</div>
                    <div className="text-right">
                        <a href={"/testimony"} className="text-danger">{data.link} >> </a>
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