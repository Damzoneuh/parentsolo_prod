import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import 'bootstrap/dist/css/bootstrap.css';
import '../../../sass/global.scss';
import SelectPayment from "./SelectPayment";
import CardEntries from "./CardEntries";
import Logger from "../../common/Logger";
import axios from 'axios';
import KnowCards from "./KnowCards";
import Paypal from "./Paypal";

export default class Payment extends Component{
   constructor(props){
       super(props);
       let doc = document.getElementById('payment');
       let settings = JSON.parse(doc.dataset.settings);
       let token = doc.dataset.token;
       this.state = {
           isLoaded: false,
           settings: settings,
           token: token,
           tab: 1,
           message: {
               message: null,
               type: null
           },
           cards: [],
           item: {}
       };
       axios.get('/api/payment/profil')
           .then(res => {
               this.setState({
                   cards: res.data
               });
               axios.get('/api/items/' + this.state.settings.itemId)
                       .then(res => {
                           this.setState({
                               item: res.data,
                               isLoaded: true
                           })
                       });
           });
       this.tabHandler = this.tabHandler.bind(this);
       this.loggerHandler = this.loggerHandler.bind(this);
   }

   tabHandler(tab){
       this.setState({
           tab: tab
       })
   }

   loggerHandler(data){
       this.setState({
           message: {
               type: data.type,
               message: data.message
           }
       })
   }


    render() {
       const {settings, message, tab, token, cards, isLoaded, item} = this.state;
       if (!isLoaded){
           return (
              <div>

              </div>
           )
       }
       if (tab === 1 && isLoaded) {
           return (
               <div>
                   <Logger message={message.message} type={message.type}/>
                   <SelectPayment handler={this.tabHandler}/>
                   {cards.length > 0 ? <KnowCards cards={cards} item={item} token={token} settings={settings} logger={this.loggerHandler}/> : ''}
               </div>
           );
       }
       if (tab === 2 && isLoaded){
           return (
               <div>
                   <Logger message={message.message} type={message.type}/>
                   <CardEntries handler={this.tabHandler} item={item} token={token} settings={settings} logger={this.loggerHandler}/>
               </div>
           )
       }
       if (tab === 3 && isLoaded){
           return (
               <Paypal handler={this.tabHandler} item={item} token={token} settings={settings} logger={this.loggerHandler}/>
           )
       }
    }

}

ReactDOM.render(<Payment/>, document.getElementById('payment'));