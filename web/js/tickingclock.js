var currentTime;
function setDate(theDate)
{
	currentTime = new Date(theDate);
	setTimeout('updateTime()', 1000);
	display();
}
function updateTime()
{
	var seconds = currentTime.getSeconds();
	currentTime.setSeconds(seconds + 1);
	setTimeout('updateTime()', 1000);
	display();
}
function display()
{
	var hours = currentTime.getHours();
	var meridian;
	var minutes = currentTime.getMinutes();
	var seconds = currentTime.getSeconds();
	if (seconds < 10) seconds = "0" + seconds;
	if (minutes < 10) minutes = "0" + minutes;
	if (hours > 11) meridian = "PM";
	else meridian = "AM";
	hours = hours % 12;
	if (hours == 0) hours = 12;
	document.getElementById("clockTime").innerHTML = hours + ":" + minutes + ":" + seconds + " " + meridian;
}
