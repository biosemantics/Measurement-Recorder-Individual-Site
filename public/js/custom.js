/**
 * Created by ZEUS on 5/24/2018.
 */
var piemenu = new wheelnav('piemenu');
piemenu.spreaderInTitle = '+';
piemenu.spreaderOutTitle = 'Details';
piemenu.spreaderRadius = piemenu.wheelRadius * 0.25;
piemenu.clockwise = false;
piemenu.wheelRadius = piemenu.wheelRadius * 0.99;
// piemenu.length = 10;
// piemenu.createWheel();

var testFunction = function (id) {
    alert(id);
};