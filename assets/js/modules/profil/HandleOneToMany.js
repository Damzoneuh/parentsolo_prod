import React, {Component} from 'react';
import axios from 'axios';
import Logger from "../../common/Logger";

export default class HandleOneToMany extends Component{
    constructor(props) {
        super(props);
        this.state = {
            message: null,
            type: null
        };
        this.handleSend = this.handleSend.bind(this);
    }

    handleSend(e){
        let data = {name: this.props.tableName, id: e.target.value};
        axios.put('/api/add/otm', data)
            .then(res => {
                this.setState({
                    message: res.data,
                    type: 'success'
                });
                setInterval(() => this.setState({
                    message: null,
                    type: null
                }), 2000)
            })
    }


    render() {
        const {user, trans, tables, tableName, userField, catField} = this.props;
        const {message, type} = this.state;
        return (
            <form className="form" onChange={this.handleSend}>
                {message && type ? <Logger message={message} type={type} /> : ''}
                <div className="form-group">
                    <select>
                        <option value={null}>{trans["I keep it to myself."]} </option>
                        {tables[tableName] ? tables[tableName].map(table => {
                            let defaultChecked = false;
                            if (user[catField][userField]){
                                if (user[catField][userField] === trans[table.name]){
                                    defaultChecked = true
                                }
                            }
                            return(
                                <option key={table.id} value={table.id} selected={defaultChecked}>{trans[table.name]}</option>
                            )
                        }) : ''}
                    </select>
                </div>
            </form>
        );
    }


}