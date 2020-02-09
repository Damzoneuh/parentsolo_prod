import React, {Component} from 'react';

export default class ToastByPage extends Component{
    constructor(props) {
        super(props);

    }


    render() {
        const {message} = this.props;
        return (

                <div className="toast" aria-live="polite" aria-atomic="true" style={{position: 'absolute', top: 0, right: 0, zIndex: '1500', minHeight: '220px'}}>
                    <div className="toast-header">
                        <button type="button" className="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div className="toast-body">
                       {message}
                    </div>
                </div>

        );
    }


}