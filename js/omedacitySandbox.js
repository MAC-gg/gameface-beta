import $, { data } from 'jquery';
import { OmedaCityClient } from 'omedacity-js';

class omdeacitySandbox {
    constructor() {
        console.log("omdeacitySandbox.js loaded.");

        // ELEMENTS
        this.gameIDField = $('input#field-gameID');
        this.winnerField = $('select#field-winner');
        this.outputBox = $("#cw-game-preview");
        this.confirmBox = $("#cw-confirm-box");

        // DATA
        this.team1 = this.outputBox.data("team1");
        this.team2 = this.outputBox.data("team2");

        // EVENT LISTENER
        this.events();
    }

    events() {
        this.gameIDField.on("keyup", this.formLogic.bind(this));
        this.winnerField.on("change", this.formLogic.bind(this));
    }

    formLogic() {
        // wait for validation.js to run, then check fields
        setTimeout(this.validCheck.bind(this), 150);
    }

    validCheck() {
        let is_valid_gameID = !this.gameIDField.hasClass('is-invalid') && this.gameIDField.val() != "";
        let is_valid_winner = !this.winnerField.hasClass('is-invalid') && this.winnerField.val() != "";
        if( is_valid_gameID && is_valid_winner ) {
            this.outputBox.html('SPINNER');
            this.getGame();
        } else {
            this.outputBox.html('');
        }
    }

    getGame() {
        const client = new OmedaCityClient();
        let winnerSelected = $('select#field-winner option:selected');
        let winner = winnerSelected.text();
        let loser = winnerSelected.text() == this.team1 ? this.team2 : this.team1;
        
        client.matches.getById(this.gameIDField.val()).then((result) => {
            let dawnscore = 0;
            let duskscore = 0;
            result.players.map( dataItem => dataItem.team == "dawn" ? dawnscore += dataItem.kills : duskscore += dataItem.kills );
            let wscore = duskscore > dawnscore ? duskscore : dawnscore;
            let lscore = duskscore < dawnscore ? duskscore : dawnscore;

            let winnerHTML = `${winner}: ${wscore}`;
            let loserHTML = `${loser}: ${lscore}`;

            this.outputBox.html(`
                ${winnerHTML} vs. ${loserHTML}
                <input type="hidden" name="field-wscore" value="${wscore}">
                <input type="hidden" name="field-lscore" value="${lscore}">
                ${result.players.map( dataItem => `
                    <h3>${dataItem.display_name}</h3>
                    <input type="text" name="field-player1kills" value="${dataItem.kills}">
                    <input type="text" name="field-player1assists" value="${dataItem.assists}">
                    <input type="text" name="field-player1dmgtaken" value="${dataItem.total_damage_taken_from_heroes}">
                    <input type="text" name="field-player1physdmg" value="${dataItem.physical_damage_dealt_to_heroes}">
                    <input type="text" name="field-player1magicdmg" value="${dataItem.magical_damage_dealt_to_heroes}">
                ` ).join('')}
            `);

            this.confirmBox.removeClass("hidden");
        }).catch(err => alert(err));
    }
}

let OBJomdeacitySandbox = new omdeacitySandbox();