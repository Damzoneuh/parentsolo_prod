import React , {Component} from 'react';
import LogoForLang from "./LogoForLang";
import ImageRenderer from "./ImageRenderer";

export default class TextAreaModal extends Component{
    constructor(props) {
        super(props);
        this.state = {
            text: null,
            selectedFlower: 1
        };
        this.handleClose = this.handleClose.bind(this);
        this.handleSend = this.handleSend.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleSelectedFlower = this.handleSelectedFlower.bind(this);
    }

    handleClose(){
        this.props.handleClose();
    }

    handleSend(e){
        e.preventDefault();
        this.props.handleSend({
            id: this.state.selectedFlower,
            text: this.state.text,
            action: 'flower'
        });
    }

    handleChange(e){
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    handleSelectedFlower(value){
        this.setState({
            selectedFlower: value
        })
    }

    render() {
        const {flowers, validate, title} = this.props;
        const {selectedFlower} = this.state;
        let desc = null;
        return (
            <div className="custom-modal">
                <div className="d-flex flex-row justify-content-end">
                    <button type="button" className="close" onClick={this.handleClose}>
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div className="text-center">
                    <LogoForLang color={'black'} baseline={true} />
                </div>
                <div className="text-center">
                    <h2>{title}</h2>
                </div>
                <form className="text-center custom-modal-content" onSubmit={this.handleSend}>
                        <a className="nav-link dropdown-toggle" href="#" id="flowerDropdownMenuLink"
                           role="button" data-toggle="dropdown" aria-haspopup="true" title={selectedFlower ? flowers[selectedFlower - 1][1].description : ''}><ImageRenderer id={selectedFlower ? flowers[selectedFlower - 1][1].img : ''} className={"marg-10 flowers"} alt={"flower"}/></a>
                        <div className="dropdown-menu" aria-labelledby="flowerDropdownMenuLink">
                            {flowers.map(flower => {
                                let flowerId = flower[1].id;
                                return(
                                    <a className="dropdown-item" onClick={() => this.handleSelectedFlower(parseInt(flowerId))} key={flowerId} ><ImageRenderer id={flower[1].img} className={"marg-10 flowers"} alt={"flower"}/></a>
                                )
                            })}
                        </div>

                    <textarea className="form-control" name="text" onChange={this.handleChange}/>
                    <div className="d-flex flex-row justify-content-around align-items-center">
                        <button className="btn btn-primary btn-group" onClick={this.handleSend}>{validate}</button>
                    </div>
                </form>
            </div>
        );
    }


}