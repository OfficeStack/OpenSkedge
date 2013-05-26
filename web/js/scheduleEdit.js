classes = Array("p0","p1","p2","p3");

var oldclass;  //-- variable to hold the old class, while the mouse is over a cell, so that the old color can be restored when the mouse leaves the cell

//-- highlite a table cell when the mouse is over it
function hilitecell(cell) {
    oldclass = cell.className;
    index = 0;
    classIndex = 0;
    for (i = 0; i < document.scheduleform.priority.length; i++) {
        if (document.scheduleform.priority[i].checked) {
            classIndex=i;
        }
    }
    cell.className = classes[document.scheduleform.priority[classIndex].value];
}

//-- restore the original color when the mouse leaves the cell
function unhilitecell(cell) {
    cell.className = oldclass;
}

//-- function called when the use clicks in a cell
//-- cell is the javascript object for the cell the mouse was clicked in
//-- i is the hour
//-- j is the day
function setcell(cell, i, j) {
    //-- get the hours array for the selected day
    hour = document.getElementById('day'+j);
    //-- get the selected color

    classIndex = 0;
    for (k = 0; k < document.scheduleform.priority.length; k++) {
        if (document.scheduleform.priority[k].checked) {
            classIndex=k;
        }
    }
    //-- newval is the priority the user wants to place
    //-- 0 is unavailable
    //-- 1-3 are preference levels
    newval = document.scheduleform.priority[classIndex].value;

    //-- update the hours array
    if (hour) {
        curval = hour.value.charAt(i);
        cell.className = classes[document.scheduleform.priority[classIndex].value];
        oldclass = cell.className;
        // TODO: sectiondiv should be declared before linking this script.
        for (k = 0; k < sectiondiv; k++) {
            hour.value = hour.value.substring(0,(i+k)) + newval + hour.value.substring(i+k+1);
        }
        //-- if the old value was unavailable and the new value is above 0 then subtract from the hours list
        if (curval == 0 && newval > 0) {
            $('#maxleft').text(parseFloat($('#maxleft').text()) - (0.25 * sectiondiv));
            $('#totalhours').text(parseFloat($('#totalhours').text()) + (0.25 * sectiondiv));
            if ($('#minleft').text() > 0) {
                $('#minleft').text(parseFloat($('#minleft').text()) - (0.25 * sectiondiv));
            }
        }
        //-- if the old value was a preference level and the new value is unavailable then add to the hours list
        else if (curval > 0 && newval == 0) {
            $('#maxleft').text(parseFloat($('#maxleft').text()) + (0.25 * sectiondiv));
            $('#totalhours').text(parseFloat($('#totalhours').text()) - (0.25 * sectiondiv));
            if ($('#totalhours').text() < usermin) {
                $('#minleft').text(parseFloat($('#minleft').text()) + (0.25 * sectiondiv));
            }
        }

        if ($('#minleft').text() > 0) {
            $('#minleft').removeClass('badge-success');
            $('#minleft').addClass('badge-important');
        } else {
            $('#minleft').removeClass('badge-important');
            $('#minleft').addClass('badge-success');
        }
    }
}

//-- function to fill in a day with a certain color
function fillDay(j) {
    for (i = 0; i < 96; i++) {
        cell = document.getElementById('cell-'+i+'-'+j);
        if (cell) {
            if (cell.name != "assigned") {
                setcell(cell, i, j);
            }
        }
    }
    return false;
}
