import React, {Component} from 'react';
import Image from "./Image";
import axios from 'axios';

export default class ImageCrop extends Component{
    constructor(props) {
        super(props);
        this.state = {
            trans: null
        }
    }

    componentDidMount(){
        axios.get('/api/trans/all')
            .then(res => {
                this.setState({
                    trans: res.data
                })
            })
    }


    render() {
        const {trans} = this.state;
        return (
            <div className="modal fade bd-crop-modal-lg" tabIndex="-1" role="dialog"
                 aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div className="modal-dialog modal-lg">
                    <div className="modal-content">
                        <div className="container">
                            <div className="modal-header">
                                <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <Image trans={trans}/>
                        </div>
                    </div>
                </div>
            </div>
        );
    }


}