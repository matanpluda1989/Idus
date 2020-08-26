function func() {
    const chars = {
                    א:1, ב:2, ג:3, ד:4, ה:5, ו:6, ז:7, ח:8, ט:9, י:10, כ:20, ך:20, ל:30,
                    מ:40, ם:40, נ:50, ן:50, ס:60, ע:70, פ:80, ף:80, צ:90, ץ:90, ק:100,
                    ר:200, ש:300, ת:400
                };

    var input = document.getElementById("inputField").value; 
    
    var res = 0, ch, val;
    var lenIn = input.length;
    for(var i = 0; i < lenIn; i++)
    {
        ch = input.charAt(i)
        val = chars[ch];
        if(val === undefined)
            val = 0;
        res = res + val;
    }

    document.getElementById("score").innerHTML = res + " - ערך גימטרי";
    document.getElementById("score").style.visibility = "visible";

    document.getElementById("hi").value = "";
}