colors = Array("#BBbbBB","#AAaaFF","#CCccFF","#EEeeFF");

var oldcolor;       //-- variable to hold the old color, while the mouse is over a cell, so that the old color can be restored when the mouse leaves the cell

//-- highlite a table cell when the mouse is over it
function hilitecell(cell) {
    oldcolor = cell.style.backgroundColor;
    index = 0;
    colorIndex = 0;
    for(i=0; i<document.scheduleform.priority.length; i++) {
        if (document.scheduleform.priority[i].checked) colorIndex=i;
    }
    cell.style.backgroundColor = colors[document.scheduleform.priority[colorIndex].value];
}

//-- restore the original color when the mouse leaves the cell
function unhilitecell(cell) {
    cell.style.backgroundColor = oldcolor;
}

//-- function called when the use clicks in a cell
//-- cell is the javascript object for the cell the mouse was clicked in
//-- i is the hour
//-- j is the day
function setcell(cell, i, j) {
    //-- get the hours array for the selected day
    hour = document.getElementById('day'+j);
    //-- get the selected color

    colorIndex = 0;
    for(k=0; k<document.scheduleform.priority.length; k++) {
        if (document.scheduleform.priority[k].checked) colorIndex=k;
    }
    //-- newval is the priority the user wants to place
    //-- 0 is unavailable
    //-- 1-3 are preference levels
    //-- 4-5 are unused preference levels
    //-- 6 is for courses
    //-- 7 is for comments
    newval = document.scheduleform.priority[colorIndex].value;

    //-- update the hours array
    if (hour) {
        curval = hour.value.charAt(i);
        cell.style.backgroundColor = colors[document.scheduleform.priority[colorIndex].value];
        oldcolor = cell.style.backgroundColor;
        // TODO: sectiondiv should be declared before linking this script.
        for(k = 0; k < sectiondiv; k++) hour.value = hour.value.substring(0,(i+k)) + newval + hour.value.substring(i+k+1);
        /*//-- if the old value was unavailable and the new value is above 0 then subtract from the hours list
        if ((curval == 0)&&(newval>0)) {
            document.scheduleform.maxleft.value = parseFloat(document.scheduleform.maxleft.value) - 1/<?php print $sections_in_hour; ?>;
            document.scheduleform.totalhours.value = parseFloat(document.scheduleform.totalhours.value) + 1/<?php print $sections_in_hour; ?>;
            if (document.scheduleform.minleft.value>0) {
                document.scheduleform.minleft.value = parseFloat(document.scheduleform.minleft.value) - 1/<?php print $sections_in_hour; ?>;
            }
        }
        //-- if the old value was a preference level and the new value is unavailable then add to the hours list
        else if ((curval > 0) && (newval==0)) {
            document.scheduleform.maxleft.value = parseFloat(document.scheduleform.maxleft.value) + 1/<?php print $sections_in_hour; ?>;
            document.scheduleform.totalhours.value = parseFloat(document.scheduleform.totalhours.value) - 1/<?php print $sections_in_hour; ?>;
            if (document.scheduleform.totalhours.value < <?php print $user->min; ?>) document.scheduleform.minleft.value = parseFloat(document.scheduleform.minleft.value) + 1/<?php print $sections_in_hour; ?>;
        }*/
    }
}

//-- function to fill in a day with a certain color
function fillDay(j) {
    for(i=0; i<96; i++) {
        cell = document.getElementById('cell-'+i+'-'+j);
        if (cell) {
            if (cell.name!="assigned") setcell(cell, i, j);
        }
    }
    return false;
}
