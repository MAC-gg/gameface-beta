import $ from 'jquery';
import { OmedaCityClient } from 'omedacity-js';

class omdeacitySandbox {
    constructor() {
        console.log("omdeacitySandbox.js loaded.");
        const client = new OmedaCityClient();
        const match = client.matches.getById("c7d66eaa669c41f3af56e202977e372e");
        console.log(match);
    }
}

let OBJomdeacitySandbox = new omdeacitySandbox();