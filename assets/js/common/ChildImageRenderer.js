import React, {Component} from 'react';
import ImageRenderer from "./ImageRenderer";

export default class ChildImageRenderer extends Component{
    constructor(props) {
        super(props);
    }


    render() {
        const {child, alt, className} = this.props;
        if (child.img){
            return(
                <div>
                    {child.img.map(img => {
                        return(
                            <ImageRenderer id={img.id} alt={alt} className={className} />
                        )
                    })}
                </div>
            )
        }
    }

}