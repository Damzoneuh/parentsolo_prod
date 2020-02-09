import React , {Component} from 'react'
import axios from 'axios';

export default class News extends Component{
    constructor(props) {
        super(props);
        this.state = {
            isLoaded: false,
            data: []
        }
    }

    componentDidMount(){
        axios.get('/api/news/3')
            .then(res => {
                this.setState({
                    data: res.data,
                    isLoaded: true
                })
            })
    }

    render() {
        const {isLoaded, data} = this.state;
        if (isLoaded && typeof data[0] !== 'undefined'){
            return (
                <div className="marg-top-20">
                    <h3>{data[0].news.toUpperCase()}</h3>
                    {data.map(d => {
                        return (
                            <div key={d.id}>
                                <ul className="pad-left-inner"><li><h4>{d.title}</h4></li></ul>
                                <div className="hidden-text"> <p>{d.text}</p></div>
                                <div className="text-right"><a href={"/news"} className="text-danger">{d.viewMore}</a></div>
                            </div>
                        )
                    })}
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