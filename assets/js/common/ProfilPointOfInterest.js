import React, {Component} from 'react';
import axios from 'axios';
import DropDownProfile from "./DropDownProfile";
import Cooking from '../../fixed/IcoCooking.svg';
import Hobbies from '../../fixed/IcoHobbies.svg';
import Lang from '../../fixed/IcoLang.svg';
import Movie from '../../fixed/IcoMovie.svg';
import Music from '../../fixed/IcoMusic.svg';
import Outing from '../../fixed/IcoOuting.svg';
import Pets from '../../fixed/IcoPets.svg';
import Reading from '../../fixed/IcoReading.svg';
import Sport from '../../fixed/IcoSport.svg';

export default class ProfilPointOfInterest extends Component{
    constructor(props) {
        super(props);
    }


    render() {
        const {profile, trans} = this.props;
        return (
            <div className="rounded-more bg-light text-center marg-top-20 marg-bottom-20 pad-10">
                <h3>{trans["point.of.interest"]}</h3>
                <DropDownProfile content={profile.outings} title={trans.outing} img={Outing} imgClass={"icon-3"}/>
                <DropDownProfile content={profile.cooking} title={trans.cooking} img={Cooking} imgClass={"icon-3"}/>
                <DropDownProfile content={profile.hobbies} title={trans.hobbies} img={Hobbies} imgClass={"icon-3"}/>
                <DropDownProfile content={profile.sport} title={trans.sports} img={Sport} imgClass={"icon-3"}/>
                <DropDownProfile content={profile.music} title={trans.music} img={Music} imgClass={"icon-3"}/>
                <DropDownProfile content={profile.movie} title={trans.movie} img={Movie} imgClass={"icon-3"}/>
                <DropDownProfile content={profile.read} title={trans.reading} img={Reading} imgClass={"icon-3"}/>
                <DropDownProfile content={profile.pet} title={trans.pets} img={Pets} imgClass={"icon-3"}/>
                <DropDownProfile content={profile.lang} title={trans["spoken.languages"]} img={Lang} imgClass={"icon-3"}/>
            </div>
        );
    }


}