import React, {Component} from 'react';
import ReactDOM from 'react-dom';
let el = document.querySelector('#viewer');

export default class ImageViewer extends Component{
    constructor(props) {
        super(props);
        this.state = {
            isLoaded: false,
        }
    }


    render() {
        if (el.dataset.path && el.dataset.alt && el.dataset.class){
            return (
                <img src={'https://parentsolo.backndev.fr/api/img/render/' + el.dataset.path} alt={el.dataset.alt} className={el.dataset.class}/>
            );
        }
    }
}
ReactDOM.render(<ImageViewer/>, document.getElementById('viewer'));
