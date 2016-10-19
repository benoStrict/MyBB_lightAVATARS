/* 
 * The MIT License
 *
 * Copyright 2016 Arthur.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

NodeList.prototype.forEach = Array.prototype.forEach;

var link = document.createElement("link");
link.setAttribute("rel", "stylesheet");
linktostyles="../resources/"+linktostyles;
link.setAttribute("href", linktostyles);
var head = document.getElementsByTagName("head")[0];
head.appendChild(link);
var licznik=1;

window.onload = function() {
    document.getElementById('setting_lightavatars_view').innerHTML="";
    var info = document.querySelectorAll(".text_input").forEach(listOfOptions);
    nextline();
};

function nextline() {
    var selected = document.getElementById('setting_lightavatars_view');
    var optionvalue=selected.options[selected.selectedIndex].text;
    var avatarvalue=document.getElementById(optionvalue).value;
    avatarvalue=avatarvalue.split(' ');
    avatarvalue='lavatar-'+avatarvalue.join(' lavatar-');
    document.getElementById('la_avatar').className=avatarvalue;
    
    selected.addEventListener("change", function(){
        optionvalue=selected.options[selected.selectedIndex].text;
        var avatarchagevalue=document.getElementById(optionvalue).value;
        avatarchagevalue=avatarchagevalue.split(' ');
        avatarchagevalue='lavatar-'+avatarchagevalue.join(' lavatar-');
        document.getElementById('la_avatar').className=avatarchagevalue;

        document.getElementById(optionvalue).addEventListener("change", function(){
            var avatarchagevalue=document.getElementById(optionvalue).value;
            avatarchagevalue=avatarchagevalue.split(' ');
            avatarchagevalue='lavatar-'+avatarchagevalue.join(' lavatar-');
            document.getElementById('la_avatar').className=avatarchagevalue;
        });
    });
    document.getElementById(optionvalue).addEventListener("change", function(){
        var avatarchagevalue=document.getElementById(optionvalue).value;
        avatarchagevalue=avatarchagevalue.split(' ');
        avatarchagevalue='lavatar-'+avatarchagevalue.join(' lavatar-');
        document.getElementById('la_avatar').className=avatarchagevalue;
    });
}

function listOfOptions(element) {
    var optionbutton = document.createElement("option");
    optionbutton.setAttribute("value", licznik);
    if(licznik==1) {
        optionbutton.setAttribute("selected","selected");
    }
    optionbutton.innerHTML=element.id;
    document.getElementById('setting_lightavatars_view').appendChild(optionbutton);
    licznik++;
}
