import React, {Component} from 'react';
import axios from'axios';
import logoFrBaselineBlack from '../../fixed/Logo_ParentsoloFR_Noir.png';
import logoFrBlack from '../../fixed/Logo_ParentsoloFR_Noir_sansBL.png';
import logoFrWhite from '../../fixed/Logo_ParentsoloFR_Blanc.png'
import logoDeBaselineBlack from '../../fixed/Logo_ParentsoloDE_Noir.png';
import logoDeBlack from '../../fixed/Logo_ParentsoloDE_Noir_sansBL.png';
import logoDeWhite from '../../fixed/Logo_ParentsoloDE_Blanc.png';
import logoEnBaselineBlack from '../../fixed/Logo_ParentsoloEN_Noir.png';
import logoEnBlack from '../../fixed/Logo_ParentsoloEN_Noir_sansBL.png';
import logoEnWhite from '../../fixed/Logo_ParentsoloEN_Blanc.png';

export default class LogoForLang extends Component{
    constructor(props) {
        super(props);
        this.state = {
            isLoaded: false,
            lang: null
        };
        axios.get('/api/lang')
            .then(res => {
                this.setState({
                    lang: res.data,
                    isLoaded: true
                })
            })
    }

    render() {
        const {lang, isLoaded} = this.state;
        const {className, alt, baseline, color} = this.props;
       if (isLoaded){
           if (lang === 'fr'){
               if (color === 'white'){
                   return (<img src={logoFrWhite} alt={alt} className={className} />)
               }
               else {
                   if (baseline){
                       return (<img className={className} alt={alt} src={logoFrBaselineBlack} />)
                   }
                   return (<img src={logoFrBlack} alt={alt} className={className}/>)
               }
           }
           if (lang === 'de'){
               if (color === 'white'){
                   return (<img src={logoDeWhite} className={className} alt={alt}/>)
               }
               else {
                   if (baseline){
                       return (<img src={logoDeBaselineBlack} alt={alt} className={className}/>)
                   }
                   return (<img src={logoDeBlack} alt={alt} className={className}/>)
               }
           }
           if (lang === 'en'){
               if (color === 'white'){
                   return (<img src={logoEnWhite} className={className} alt={alt}/>)
               }
               else {
                   if (baseline){
                       return (<img src={logoEnBaselineBlack} alt={alt} className={className}/>)
                   }
                   return (<img src={logoEnBlack} className={className} alt={alt} />)
               }
           }
       }
       else {
           return (<div></div>)
       }
    }

}