import React, {Component} from 'react';

export default class Pagination extends Component{
    constructor(props) {
        super(props);
        let display = [];
        for (let i = 0; i < this.props.itemsPerPage; i++){
            display.push(this.props.data[i])
        }

        this.state = {
            data: this.props.data,
            offset: 1,
            selected: 0,
            pageNumber: 0,
            modulo: 0,
            displayed: display
        };

        this.renderLinks = this.renderLinks.bind(this);
        this.handleNext = this.handleNext.bind(this);
        this.handlePrevious = this.handlePrevious.bind(this);
        this.handleDisplay = this.handleDisplay.bind(this);
        this.handlePaginationLink = this.handlePaginationLink.bind(this);
    }

    componentDidMount(){
        let modulo = this.state.data.length % this.props.itemsPerPage;
        let pageNumber = this.float2int(this.state.data.length / this.props.itemsPerPage);
        this.setState({
            pageNumber: modulo > 0 ? pageNumber + 1 : pageNumber,
            modulo: modulo
        });
        setTimeout(() => this.props.handleDisplay(this.state.displayed), 200)
    }

    renderLinks(){
       if (this.state.offset === 1){
           return (
               <div>
                   <a className={"scaled"} onClick={() => this.handlePaginationLink(this.state.offset)}>{this.state.offset}</a>
                   {this.state.pageNumber > 1 ? <a className={"marg-10"} onClick={() => this.handlePaginationLink(this.state.offset + 1)}>{this.state.offset + 1}</a> : ''}
                   {this.state.pageNumber > 2 ? <a className={"marg-10"} onClick={() => this.handlePaginationLink(this.state.offset + 2)}>{this.state.offset + 2}</a> : ''}
                   .... <a className={"marg-10"} onClick={() => this.handlePaginationLink(this.state.pageNumber)}>{this.state.pageNumber}</a>
               </div>
           )
       }
       else if (this.state.offset === this.state.pageNumber){
           return (
               <div>
                   {this.state.pageNumber > 2 ? <a className={"marg-10"} onClick={() => this.handlePaginationLink(this.state.offset - 2)}>{this.state.offset - 2}</a> : ''}
                   {this.state.pageNumber > 1 ? <a className={"marg-10"} onClick={() => this.handlePaginationLink(this.state.offset - 2)}>{this.state.offset - 1}</a> : ''}
                   <a className={"scaled"} onClick={() => this.handlePaginationLink(this.state.offset)}>{this.state.offset}</a>
                   .... <a className={"marg-10"} onClick={() => this.handlePaginationLink(this.state.pageNumber)}>{this.state.pageNumber}</a>
               </div>
           )
       }
       else {
           return (
               <div>
                   {this.state.pageNumber > 1 ? <a className={"marg-10"} onClick={() => this.handlePaginationLink(this.state.offset - 1)}>{this.state.offset - 1}</a> : ''}
                   <a className={"scaled"} onClick={() => this.handlePaginationLink(this.state.offset)}>{this.state.offset}</a>
                   {this.state.pageNumber > 2 ? <a className={"marg-10"} onClick={() => this.handlePaginationLink(this.state.offset + 1)}>{this.state.offset + 1}</a> : ''}
                   .... <a className={"marg-10"} onClick={() => this.handlePaginationLink(this.state.pageNumber)}>{this.state.pageNumber}</a>
               </div>
           )
       }
    }

    handleNext(){
        if (this.state.offset <= this.state.pageNumber){
            this.setState({
                offset: this.state.offset + 1,
                selected: this.state.selected + 1
            });
        }
        setTimeout(() => this.handleDisplay(), 200)
    }

    handlePrevious(){
        if (this.state.offset > 1){
            this.setState({
                offset: this.state.offset - 1,
                selected: this.state.selected - 1
            });
            setTimeout(() => this.handleDisplay(), 200)
        }
    }

    handleDisplay(){
        let nextState = [];
        this.setState({
            displayed: null
        });
        if (this.state.modulo === 0){
            for (let i = 0; i < this.props.itemsPerPage; i++){
                nextState.push(this.state.data[i + this.state.selected])
            }
        }
        else {
            for (let i = 0; i < this.props.itemsPerPage; i++){
                if (this.state.pageNumber === this.state.offset){
                    if (i <= this.state.modulo){
                        nextState.push(this.state.data[i + this.state.selected])
                    }
                }
                else {
                    nextState.push(this.state.data[i + this.state.selected])
                }
            }
        }

        this.setState({
            displayed: nextState
        });
        this.props.handleDisplay(this.state.displayed);
    }

    handlePaginationLink(val){
        this.setState({
            selected: val === 0 ? 0 : val -1,
            offset: val === 0 ? val + 1 : val
        });
        setTimeout(() => this.handleDisplay(), 200);
    }

    float2int(value){
        return value | 0
    }

    render() {
        const {pageNumber, offset}  = this.state;
        const {trans} = this.props;
        return (
            <div className="d-flex flex-row justify-content-between align-items-center">
                <button className="btn btn-group btn-outline-danger" onClick={this.handlePrevious}>{trans.previous}</button>
                    {this.renderLinks()}
                <button className="btn btn-group btn-outline-danger" onClick={this.handleNext}>{trans.next}</button>
            </div>
        );
    }


}