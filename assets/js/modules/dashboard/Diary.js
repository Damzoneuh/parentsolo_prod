import React, {Component} from 'react';
import axios from 'axios';
import diaryImg from '../../../fixed/Agenda.png';
import ImageRenderer from "../../common/ImageRenderer";

export default class Diary extends Component{
    constructor(props) {
        super(props);
        this.state = {
            diary: [],
            isLoaded: false
        }
    }

    componentDidMount(){
        axios.get('/api/diary')
            .then(res => {
                this.setState({
                    diary: res.data,
                    isLoaded: true
                })
            })
    }

    render() {
        const {isLoaded, diary} = this.state;
        if (isLoaded){
            if (!diary.title){
                return (
                    <div className="diary-wrap diary-wrap-dashboard d-flex flex-column justify-content-around h-100 marg-top-10">
                        <div className="d-flex flex-row justify-content-between marg-10">
                            <h4>{diary.diary.toUpperCase()}</h4>
                            <img src={diaryImg} alt="diary" className="diary-img" />
                        </div>
                        {diary.text}
                        <div className="text-right">
                            <a href="/diary" className="btn btn-group btn-success marg-top-10">{diary.shareEvent}</a>
                        </div>
                    </div>
                )
            }
            return (
                <div className="diary-wrap diary-wrap-dashboard d-flex flex-column justify-content-around h-100 marg-top-10">
                    <div className="d-flex flex-row justify-content-between marg-10">
                        <div className="w-50 text-left">
                            <h4>{diary.diary.toUpperCase()}</h4>
                            <h4>{diary.title}</h4>
                            <h4 className="date">{diary.date}</h4>
                            <h5>{diary.location}</h5>
                        </div>
                        <div className="w-50 text-right">
                            {diary.img ? <ImageRenderer id={diary.img} alt={"diary image"} className={"diary-img diary-img-55"}/> :
                                <img src={diaryImg} alt="diary picture" className="diary-img diary-img-55" />}
                        </div>
                    </div>
                    <div className="hidden-text"> {diary.text}</div>
                    <div className="text-center">
                        <a href="/diary" className="btn btn-group btn-outline-light marg-10">{diary.shareEvent}</a>
                        <a href="/diary" className="btn btn-group btn-outline-light marg-10">{diary.readMore}</a>
                    </div>

                </div>
            );
        }
        else {
            return (
                <div>

                </div>
            );
        }
    }

}