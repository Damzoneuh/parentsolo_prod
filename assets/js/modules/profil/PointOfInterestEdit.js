import React, {Component} from 'react';
import Cook from '../../../fixed/IcoCooking.svg';
import Hobbies from '../../../fixed/IcoHobbies.svg';
import Lang from '../../../fixed/IcoLang.svg';
import Movie from '../../../fixed/IcoMovie.svg';
import Music from '../../../fixed/IcoMusic.svg';
import Outing from '../../../fixed/IcoOuting.svg';
import Pets from '../../../fixed/IcoPets.svg';
import Reading from '../../../fixed/IcoReading.svg';
import Sport from '../../../fixed/IcoSport.svg';
import DropDownProfileEdit from "./DropDownProfileEdit";

export default class PointOfInterestEdit extends Component{
    constructor(props) {
        super(props);
    }


    render() {
        const {trans, user, table} = this.props;
        return (
            <div className="rounded-more bg-light marg-top-20 pad-30 marg-bottom-20">
                <h3>{trans["point.of.interest"]}</h3>
                <DropDownProfileEdit table={table.Outing} tableName={"Outing"} title={trans.outing} img={Outing} imgClass={"icon-3"} trans={trans} fieldName={"outings"} user={user}/>
                <DropDownProfileEdit table={table.Cook} tableName={"Cook"} title={trans.Cooking} img={Cook} imgClass={"icon-3"} trans={trans} fieldName={"cook"} user={user} />
                <DropDownProfileEdit table={table.Hobbies} tableName={"Hobbies"} title={trans.hobbies} img={Hobbies} imgClass={"icon-3"} trans={trans} fieldName={"hobbies"} user={user} />
                <DropDownProfileEdit table={table.Sport} tableName={"Sport"} title={trans.Sport} img={Sport} imgClass={"icon-3"} trans={trans} fieldName={"sport"} user={user} />
                <DropDownProfileEdit table={table.Music} tableName={"Music"} title={trans.Music} img={Music} imgClass={"icon-3"} trans={trans} fieldName={"music"} user={user} />
                <DropDownProfileEdit table={table.Movies} tableName={"Movies"} title={trans.movie} img={Movie} imgClass={"icon-3"} trans={trans} fieldName={"movie"} user={user} />
                <DropDownProfileEdit table={table.Reading} tableName={"Reading"} title={trans.reading} img={Reading} imgClass={"icon-3"} trans={trans} fieldName={"read"} user={user} />
                <DropDownProfileEdit table={table.Pets} tableName={"Pets"} title={trans.pets} img={Pets} imgClass={"icon-3"} trans={trans} fieldName={"pet"} user={user} />
                <DropDownProfileEdit table={table.Langages} tableName={"Langages"} title={trans.Languages} img={Lang} imgClass={"icon-3"} trans={trans} fieldName={"lang"} user={user} />
            </div>
        );
    }


}