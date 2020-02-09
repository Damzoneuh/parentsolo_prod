import React, {Component} from 'react';
import axios from 'axios'
import searchGlass from '../../../fixed/Loupe.svg';

export default class LightSearch extends Component {
    constructor(props) {
        super(props);
        this.state = {
            relation: 1,
            minAge: 18,
            maxAge: 98,
            child: [],
            trans: [],
            isLoaded: false,
            active: 1,
            age: [],
            canton: [],
            cantonSelected: 2,
            childSelected: null,
            search: []
        };

        for (let i = 18; i < 99; i++) {
            this.state.age.push(i);
        }

        for (let i = 0; i < 6; i++) {
            this.state.child.push(i);
        }

        this.handleButton = this.handleButton.bind(this);
        this.handleAge = this.handleAge.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
    }

    componentDidMount() {
        axios.get('/api/trans/search')
            .then(res => {
                this.setState({
                    trans: res.data,
                });
                axios.get('/api/canton')
                    .then(res => {
                        this.setState({
                            canton: res.data,
                            isLoaded: true
                        })
                    })
            });
    }

    handleButton(value) {
        if (value === 1) {
            this.setState({
                relation: value,
                active: value
            })
        } else {
            this.setState({
                relation: value,
                active: value
            })
        }
    }

    handleAge(e) {
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    handleSubmit() {
        let data = {
            canton: this.state.cantonSelected,
            child: this.state.childSelected,
            relationship: this.state.relation,
            minAge: this.state.minAge,
            maxAge: this.state.maxAge
        };
        axios.post('/api/search', data)
            .then(res => {
               this.setState({
                   search: res.data
               });
               this.handleSearch();
            })
    }

    handleSearch(){
        this.props.handleSearch(this.state.search);
        this.props.handleTab(2)
    }


    render() {
        const {minAge, canton, child, trans, active, isLoaded, age, cantonSelected, childSelected} =this.state;
        if (isLoaded){
            return (
                <div className="col-sm-12 col-md-6">
                    <div className="rounded-more border-red marg-top-10 h-100">
                        <div className="d-flex flex-row justify-content-start align-items-center">
                        <img src={searchGlass} className="search-glass" alt="search glass"/>
                        <h3 className="marg-top-10">{trans.search.toUpperCase()}</h3>
                    </div>
                        <div className="d-flex flex-row justify-content-around align-items-center marg-top-10">
                            <button className={active === 1 ? "btn btn-group btn-outline-danger active" : "btn btn-group btn-outline-danger"} onClick={() => this.handleButton(1)}>{trans.lovely}</button>
                            <button className={active === 2 ? "btn btn-group btn-outline-danger active" : "btn btn-group btn-outline-danger"} onClick={() => this.handleButton(2)}>{trans.friendly}</button>
                        </div>
                        <div className="d-flex flex-row justify-content-between align-items-center marg-20">
                            {trans.age}
                            <select defaultChecked={minAge} name="minAge" onChange={this.handleAge}>
                                {age.map(a => {
                                    return(
                                        <option value={a} key={a}>{a}</option>
                                    )
                                })}
                            </select>
                            {trans.and}
                            <select name="maxAge" onChange={this.handleAge}>
                                {age.map(a => {
                                    return(
                                        <option value={a} key={a} selected={a === 98}>{a}</option>
                                    )
                                })}
                            </select>
                            {trans.yearsOld}
                        </div>
                        <div className="d-flex flex-row justify-content-between align-items-center marg-20">
                            {trans.canton}
                            <select name="cantonSelected" onChange={this.handleAge} defaultChecked={cantonSelected}>
                                {canton.map(c => {
                                    return (<option value={c.id} key={c.id}>{c.name}</option>)
                                })}
                            </select>
                        </div>
                        <div className="d-flex flex-row justify-content-between align-items-center marg-20">
                            {trans.child}
                            <select name="childSelected" onChange={this.handleAge}>
                                <option value={null} >{trans.indifferent}</option>
                                {child.map(ch => {
                                    return (<option value={ch} selected={ch === childSelected} key={ch}>{ch}</option>)
                                })}
                            </select>
                        </div>
                        <div className="d-flex flex-row justify-content-around align-items-center marg-20">
                            <button className="btn btn-group btn-danger btn-lg" onClick={this.handleSubmit}>{trans.search}</button>
                        </div>
                    </div>
                </div>
            );
        }
        else {
            return (<div></div>)
        }
    }

}