import React, {Component} from 'react';
import axios from 'axios';
import HBL from '../../../fixed/Coeur-Bas_R.svg';
import HBR from '../../../fixed/Coeur-Bas_L.svg';
import HTL from '../../../fixed/Coeur-Haut_R.svg';
import HTR from '../../../fixed/Coeur-Haut_L.svg';

let el = document.getElementById('dashboard');
export default class AutoSearch extends Component{
    constructor(props) {
        super(props);
        this.state = {
            isLoaded: false,
            trans: []
        };
        this.handleMatching = this.handleMatching.bind(this);
    }

    componentDidMount(){
        axios.get('/api/trans/matching')
            .then(res => {
                this.setState({
                    trans: res.data,
                    isLoaded: true
                })
            })
    }

    handleMatching(){
        axios.get('api/user/' + el.dataset.user)
            .then(res => {
                  if (res.data.isPremium){
                      axios.get('/api/matching')
                          .then(res => {
                              this.props.handleSearch(res.data);
                              this.props.handleTab(2)
                          })
                    }
                  else {
                      window.location.href = '/shop';
                  }
            })
    }

    render() {
        const {isLoaded, trans} = this.state;
        if (isLoaded){
            return (
                <div className="col-sm-12 col-md-6 font-size-20">
                    <div className={"testimony-wrap h-100"}>
                        <h3>MATCHING</h3>
                        <p className="text-center marg-top-20">{trans.cupidon}</p>
                        <div className="d-flex flex-row marg-top-50">
                            <div className="w-25">
                                <div>
                                    <img src={HTL} alt="heart" className="heart-top"/>
                                </div>
                                <div className="text-right">
                                    <img src={HBL} alt="heart" className="heart-bottom"/>
                                </div>
                            </div>
                            <div className="w-75 d-flex justify-content-around align-items-center">
                                <button className="btn-lg btn btn-group btn-outline-light launch-button" onClick={this.handleMatching}>{trans.launch} !</button>
                            </div>
                            <div className="w-25">
                                <div className="text-right">
                                    <img src={HTR} alt="heart" className="heart-top"/>
                                </div>
                                <div>
                                    <img src={HBR} alt="heart" className="heart-bottom"/>
                                </div>
                            </div>
                        </div>
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