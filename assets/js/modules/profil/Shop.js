import React, {Component} from 'react';
import axios from 'axios';

export default class Shop extends Component{
    constructor(props){
        super(props);
        this.state = {
            isLoaded: true,
            items: []
        };
        axios.get('/api/shop')
            .then(res => {
                this.setState({
                    items: res.data
                })
            });
        this.handleShop = this.handleShop.bind(this);
    }

    handleShop(id){
        window.location.href = '/payment/' + id;
    }

    render() {
        const {isLoaded, items} = this.state;
        if (!isLoaded || !items.length > 0){
            return (
                <div className="container-loader">
                    <div className="ring">
                        <span className="ring-span"></span>
                    </div>
                </div>
            )
        }

        else {
            return (
                <div className="flex-row w-50 m-auto">
                    {items.map(item => {
                        return(
                            <div className="col-12 card marg-top-10" key={item.id} onClick={() => this.handleShop(item.id)}>
                                <div className="row pad-10 flex-row justify-content-center align-items-center">
                                    <div className="col-6 text-center">
                                        {item.type}
                                    </div>
                                    <div className="col-6 text-center">
                                        {item.price}  CHF
                                    </div>
                                </div>
                            </div>
                        )
                    })}
                </div>
            );
        }
    }

}

