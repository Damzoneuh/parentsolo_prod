import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import UnderNav from "../../common/UnderNav";
import ImageRenderer from "../../common/ImageRenderer";
import defaultMan from "../../../fixed/HommeDefaut.png";
import defaultWoman from "../../../fixed/FemmeDefaut.png";

export default class Flower extends Component{
    constructor(props) {
        super(props);
        this.state = {
            flowers: null,
            trans: null
        }
    }

    componentDidMount(){
        axios.get('/api/flower/received')
            .then(res => {
                this.setState({
                    flowers: res.data
                })
            });
        axios.get('api/trans/all')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            });
        axios.put('/api/flower/read')
            .then(res => {

            })
    }


    render() {
        const {trans, flowers} = this.state;
        if (flowers && trans && flowers.length > 0){
            return (
                <div>
                    <UnderNav data={'flower'}/>
                    <div className="container">
                        <div className="row">
                            {flowers.map(f => {
                                return(
                                    <div className="col">
                                        <div className="testimony-wrap marg-bottom-20">
                                            <div className="d-flex align-items-center justify-content-between">
                                                {f.img ? <ImageRenderer id={f.img} alt={"profile image"} className={"thumb-profile-img"}/> : f.isMan ?
                                                    <img src={defaultMan} className="thumb-profile-img" alt="profile image" /> :
                                                    <img src={defaultWoman} className="thumb-profile-img" alt="profile image"/>
                                                }
                                                {f.flower.img ? <ImageRenderer id={f.flower.img} alt={'flower'} className={'thumb-profile-img'}/> : ''}
                                            </div>
                                            <div className="marg-top-10 text-center">
                                                <h3>{f.alias.toUpperCase()}</h3>
                                                <div className="marg-top-20 text-center">
                                                    <h5>{trans.description}</h5>
                                                    {f.flower.description}
                                                </div>
                                                <div className="marg-top-50 text-center">
                                                    <h5>{trans.message}</h5>
                                                    {decodeURI(f.message)}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                )
                            })}
                        </div>
                    </div>
                </div>
            );
        }

        if (flowers && trans && flowers.length === 0){
           return (
               <div>
                   <UnderNav data={'flower'}/>
                   <div className="container">
                       <div className="row">
                           <div className="col text-center marg-bottom-10 marg-top-20">
                                <h1>{trans['no.flowers']}</h1>
                           </div>
                       </div>
                   </div>
               </div>
           )
        }

        else {
            return(
                <div>

                </div>
            )
        }
    }
}

ReactDOM.render(<Flower/>, document.getElementById('flower'));